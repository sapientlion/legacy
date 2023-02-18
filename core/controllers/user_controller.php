<?php

if(session_status() === PHP_SESSION_NONE)
{
	session_start();
}

require_once(__DIR__ . '/../../config.php');
require_once(SITE_ROOT . '/core/controllers/system_controller.php');
require_once(SITE_ROOT . '/core/interfaces/iuser_controller.php');
require_once(SITE_ROOT . '/core/models/user.php');
require_once(SITE_ROOT . '/core/settings/get.php');
require_once(SITE_ROOT . '/core/settings/input.php');
require_once(SITE_ROOT . '/core/settings/paths.php');
require_once(SITE_ROOT . '/core/settings/session.php');

class UserController extends SystemController implements IUserController 
{
	private PDO $dbh;
	private User $user;
	
	/**
	 * Find matching user account names in DB.
	 *
	 * @param  string $username a user account name to check.
	 * @return int Total number of matching entries found in DB.
	 */
	private function tryToFindMatchingUserNames(string $username = '') : int
	{
		//
		// SELECT username FROM user WHERE username = :username
		//
		$string = "SELECT " . 
		DB_TABLE_USER_NAME . " FROM " . 
		DB_TABLE_USER . " WHERE " . 
		DB_TABLE_USER_NAME . " = :" . DB_TABLE_USER_NAME;

		$stmt = $this->dbh->prepare($string);

		if(empty($username))
		{
			$username = $this->user->getUsername();
		}

		$stmt->bindParam(':' . DB_TABLE_USER_NAME, $username);
		$stmt->execute();

		$result = $stmt->fetchAll();
		$result = count($result);

		return $result;
	}
	
	/**
	 * Find matching user account emails in DB.
	 *
	 * @param  string $email an email address to check.
	 * @return int Total number of matching entries found in DB.
	 */
	private function tryToFindMatchingEmails(string $email = '') : int
	{
		//
		// SELECT email FROM user WHERE email = :email
		//
		$string = "SELECT " . 
		DB_TABLE_USER_EMAIL . " FROM " . 
		DB_TABLE_USER . " WHERE " . 
		DB_TABLE_USER_EMAIL . " = :" . DB_TABLE_USER_EMAIL;

		$stmt = $this->dbh->prepare($string);

		if(empty($email))
		{
			$email = $this->user->getEmail();
		}

		$stmt->bindParam(':' . DB_TABLE_USER_EMAIL, $email);
		$stmt->execute();

		$result = $stmt->fetchAll();
		$result = count($result);

		return $result;
	}
	
	/**
	 * Validate all given user credentials.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 */
	private function validate() : bool
	{
		$username = $this->user->getUsername();
		$usernameLength = strlen($username);

		//
		// Check user name length.
		//
		if($usernameLength > DATA_USER_NAME_LENGTH)
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('UserController', 'validate', 'Given user name is too long');
			}
			
			return false;
		}

		//
		// Check user name format.
		//
		if(!preg_match('/^[\w-]+$/', $username))
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('UserController', 'validate', 'Given user name is in incorrect format');
			}

			return false;
		}

		$email = $this->user->getEmail();
		$emailLength = strlen($email);

		//
		// Check email address length.
		//
		if($emailLength <= 0)
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('UserController', 'validate', 'Given email address is too short');
			}

			return false;
		}

		if($emailLength > DATA_USER_EMAIL_LENGTH)
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('UserController', 'validate', 'Given email address is too long');
			}

			return false;
		}

		return true;
	}
		
	/**
	 * Create a new user account entry in database.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doCreate() : bool
	{
		if(isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) || !empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('UserController', 'doCreate', 'You must be logged-out in order to use this feature');
			}

			return false;
		}

		if(!$this->validate() || $this->tryToFindMatchingEntries() > 0)
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('UserController', 'doCreate', 'Given data is invalid');
			}

			return false;
		}

		$password = $this->validatePassword(
			$this->user->getPassword(), 
			$this->user->getConfirmationPassword()
		);

		if(empty($password))
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('UserController', 'doCreate', 'Unable to validate the password');
			}

			return false;
		}

		$username = $this->user->getUsername();
		$email = $this->user->getEmail();

		//
		// INSERT INTO user (username, email, password) VALUES (:username, :email, :password)
		//
		$string = "INSERT INTO " . 
		DB_TABLE_USER . " (" . 
		DB_TABLE_USER_NAME . ", " . 
		DB_TABLE_USER_EMAIL . ", " . 
		DB_TABLE_USER_PASSWORD . ") VALUES (:" . 
		DB_TABLE_USER_NAME . ", :" . 
		DB_TABLE_USER_EMAIL . ", :" . 
		DB_TABLE_USER_PASSWORD . ")";
		
		$stmt = $this->dbh->prepare($string);

		$stmt->bindParam(':' . DB_TABLE_USER_NAME, $username);
		$stmt->bindParam(':' . DB_TABLE_USER_EMAIL, $email);
		$stmt->bindParam(':' . DB_TABLE_USER_PASSWORD, $password);
		
		$result = $stmt->execute();

		return $result;
	}
	
	/**
	 * Update preceding user account by supplying it with fresh data.
	 *
	 * @param  string $currentUsername user name that is currently used by a signed-in account.
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doUpdate(string $currentUsername) : bool
	{
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) || empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('UserController', 'doUpdate', 'You must be logged-in in order to use this feature');
			}

			return false;
		}

		if(!$this->validate() || $this->tryToFindMatchingEmails() > 0)
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('UserController', 'doUpdate', 'Given data is invalid');
			}

			return false;
		}

		$password = $this->user->getPassword();
		$confirmationPassword = $this->user->getConfirmationPassword();

		if(!empty($password) || !empty($confirmationPassword))
		{
			$password = $this->validatePassword();

			if(empty($password))
			{
				if(SYSTEM_DEBUGGING)
				{
					$this->report('UserController', 'doUpdate', 'Unable to validate the password');
				}

				return false;
			}
		}

		$username = $this->user->getUsername();
		$email = $this->user->getEmail();

		if(!empty($password))
		{
			//
			// UPDATE user SET username = :username, email = :email, password = :password WHERE username = :current_username
			//
			$string = "UPDATE " . 
			DB_TABLE_USER . " SET " . 
			DB_TABLE_USER_NAME . " = :" . 
			DB_TABLE_USER_NAME . ", " . 
			DB_TABLE_USER_EMAIL . " = :" . 
			DB_TABLE_USER_EMAIL . ", " . 
			DB_TABLE_USER_PASSWORD . " = :" . 
			DB_TABLE_USER_PASSWORD . " WHERE " . 
			DB_TABLE_USER_NAME . " = :current_username";

			$stmt = $this->dbh->prepare($string);
	
			$stmt->bindParam(':' . DB_TABLE_USER_NAME , $username);
			$stmt->bindParam(':current_username', $currentUsername);
			$stmt->bindParam(':' . DB_TABLE_USER_EMAIL, $email);
			$stmt->bindParam(':' . DB_TABLE_USER_PASSWORD, $password);
	
			//
			// Reflect all user account changes on $_SESSION variables.
			//
			if($stmt->execute())
			{
				$_SESSION[SESSION_VAR_NAME_USER_NAME] = $username;
				$_SESSION[SESSION_VAR_NAME_USER_EMAIL] = $email;
				$_SESSION[SESSION_VAR_NAME_USER_PASSWORD] = $password;
	
				return true;
			}
		}

		//
		// UPDATE user SET username = :username, email = :email WHERE username = :current_username
		//
		$string = "UPDATE " . 
		DB_TABLE_USER . " SET " . 
		DB_TABLE_USER_NAME . " = :" . 
		DB_TABLE_USER_NAME . ", " . 
		DB_TABLE_USER_EMAIL . " = :" . 
		DB_TABLE_USER_EMAIL . " WHERE " . 
		DB_TABLE_USER_NAME . " = :current_username";

		$stmt = $this->dbh->prepare($string);

		$stmt->bindParam(':' . DB_TABLE_USER_NAME, $username);
		$stmt->bindParam(':current_username', $currentUsername);
		$stmt->bindParam(':' . DB_TABLE_USER_EMAIL, $email);

		//
		// Reflect all user account changes on $_SESSION variables.
		//
		if($stmt->execute())
		{
			$_SESSION[SESSION_VAR_NAME_USER_NAME] = $username;
			$_SESSION[SESSION_VAR_NAME_USER_EMAIL] = $email;

			return true;
		}

		return false;
	}
	
	/**
	 * Remove user account from database.
	 *
	 * @param  string $currentUsername user name that is currently used by a signed-in account.
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doDelete(string $currentUsername) : bool
	{
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) || empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('UserController', 'doDelete', 'You must be logged-in in order to use this feature');
			}

			return false;
		}

		if($this->tryToFindMatchingUserNames() !== 1)
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('UserController', 'doDelete', 'User account doesn`t exist');
			}

			return false;
		}

		//
		// DELETE FROM user WHERE username = :username
		//
		$string = "DELETE FROM " . 
		DB_TABLE_USER . " WHERE " . 
		DB_TABLE_USER_NAME . " = :" . 
		DB_TABLE_USER_NAME;

		$stmt = $this->dbh->prepare($string);

		$stmt->bindParam(':' . DB_TABLE_USER_NAME, $currentUsername);

		$result = $stmt->execute();
		
		return $result;
	}
	
	/**
	 * Get and set all user credentials to reveal all hidden features.
	 *
	 * @param  mixed $stmt PDO statement to process.
	 * @return array array of all defined $_SESSION variables.
	 */
	private function set(mixed $stmt) : array
	{
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		$_SESSION[SESSION_VAR_NAME_USER_NAME] = $result[1];
		$_SESSION[SESSION_VAR_NAME_USER_EMAIL] = $result[2];
		$_SESSION[SESSION_VAR_NAME_USER_PASSWORD] = $result[3];

		$_SESSION['CanCreateBlogPosts'] = $result[4];
		$_SESSION['CanReadBlogPosts'] = $result[5];
		$_SESSION['CanUpdateBlogPosts'] = $result[6];
		$_SESSION['CanDeleteBlogPosts'] = $result[7];

		$_SESSION['CanCreateComments'] = $result[8];
		$_SESSION['CanReadComments'] = $result[9];
		$_SESSION['CanUpdateComments'] = $result[10];
		$_SESSION['CanDeleteComments'] = $result[11];

		return $_SESSION;
	}
	
	/**
	 * Sign user in to the system.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doSignIn() : bool
	{
		if(isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) || !empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('UserController', 'doSignIn', 'You must be logged-in in order to access this feature');
			}

			return false;
		}

		//
		// Sign user in when user name is supplied via respective form input field.
		//
		if($this->tryToFindMatchingUserNames() === 1)
		{
			$password = $this->user->getPassword();

			//
			// Given password must comply with currently set limits.
			//
			if(empty($this->validatePassword($password, $password)))
			{
				if(SYSTEM_DEBUGGING)
				{
					$this->report('UserController', 'doUpdate', 'Unable to validate the password');
				}

				return false;
			}

			$username = $this->user->getUsername();
			//
			// SELECT password FROM user WHERE username = :username
			//
			$string = "SELECT " . 
			DB_TABLE_USER_PASSWORD . " FROM " . 
			DB_TABLE_USER . " WHERE " . 
			DB_TABLE_USER_NAME . " = :" . 
			DB_TABLE_USER_NAME;

			$stmt = $this->dbh->prepare($string);

			$stmt->bindParam(':' . DB_TABLE_USER_NAME, $username);
			$stmt->execute();

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			//
			// Given password might be different than the one saved in database.
			//
			if(!password_verify($this->user->getPassword(), $result))
			{
				if(SYSTEM_DEBUGGING)
				{
					$this->report('UserController', 'doSignIn', 'Given password is incorrect');
				}

				return false;
			}

			$string = "SELECT * FROM " . 
			DB_TABLE_USER . " WHERE " . 
			DB_TABLE_USER_NAME . " = :" . DB_TABLE_USER_NAME;

			$stmt = $this->dbh->prepare($string);
			$username = $this->user->getUsername();

			$stmt->bindParam(':' . DB_TABLE_USER_NAME, $username);
			$stmt->execute();
			$this->set($stmt);

			return true;
		}
		
		//
		// Sign user in when email address is supplied via respective form input field.
		//
		if($this->tryToFindMatchingEmails() === 1)
		{
			//
			// SELECT password FROM user WHERE email = :email
			//
			$string = "SELECT " . 
			DB_TABLE_USER_PASSWORD . " FROM " . 
			DB_TABLE_USER . " WHERE " . 
			DB_TABLE_USER_EMAIL . " = :" . DB_TABLE_USER_EMAIL;

			$stmt = $this->dbh->prepare($string);
			$email = $this->user->getEmail();

			$stmt->bindParam(':' . DB_TABLE_USER_EMAIL, $email);
			$stmt->execute();

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			//
			// Given password might be different than the one saved in database.
			//
			if($this->user->getPassword() !== $result)
			{
				if(SYSTEM_DEBUGGING)
				{
					$this->report('UserController', 'doSignIn', 'Given password is incorrect');
				}

				return false;
			}

			//
			// SELECT username, email, password FROM user WHERE email = :email
			//
			$string = "SELECT * FROM " . 
			DB_TABLE_USER . " WHERE " . 
			DB_TABLE_USER_EMAIL . " = :" . DB_TABLE_USER_EMAIL;

			$stmt = $this->dbh->prepare($string);

			$stmt->bindParam(':' . DB_TABLE_USER_EMAIL, $email);
			$stmt->execute();
			$this->set($stmt);

			return true;
		}

		return false;
	}
	
	/**
	 * __construct
	 *
	 * @param  User $user user credentials as an object.
	 * @return void on failure.
	 */
	public function __construct(?User $user = NULL)
	{
		if(is_null($user))
		{
			return;
		}

		$this->user = $user;

		try
		{
			$this->dbh = new PDO('mysql:host=' . DB_HOSTNAME . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);

			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';
		}
	}

	/**
	 * Create a new user account.
	 *
	 * @return bool on succesful execution of prepared statements.
	 */
	public function create() : bool
	{
		try
		{
			$result = $this->doCreate();

			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';

			return false;
		}
	}

	//
	// TODO add implementation.
	//
	public function read() : bool
	{
		return true;
	}
	
	/**
	 * A method wrapper used for user account update in DB.
	 *
	 * @param  string $currentUsername user name that is currently used by a signed-in account.
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function update(string $currentUsername) : bool
	{
		try
		{
			$result = $this->doUpdate($currentUsername);

			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';

			return false;
		}
	}
	
	/**
	 * A method wrapper used for user account removal from DB.
	 *
	 * @param  string $currentUsername user name that is currently used by a signed-in account.
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function delete(string $currentUsername) : bool
	{
		try
		{
			$result = $this->doDelete($currentUsername);

			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';

			return false;
		}
	}

	/**
	 * tryToFindMatchingEntries
	 *
	 * @param  string $username a user account name to check.
	 * @param  string $email an email address to check.
	 * @return int Total number of matching entries found in DB.
	 */
	public function tryToFindMatchingEntries(string $username = '', string $email = '') : int
	{
		$numOfMatchingRows = 0;

		if($numOfMatchingRows = $this->tryToFindMatchingUserNames($username) > 0)
		{
			return $numOfMatchingRows;
		}

		if($numOfMatchingRows = $this->tryToFindMatchingEmails($email) > 0)
		{
			return $numOfMatchingRows;
		}

		return $numOfMatchingRows;
	}
	
	/**
	 * Validate given password.
	 *
	 * @param  string $password password to check.
	 * @param  string $confirmationPassword confirmation password for verification.
	 * @return string Returns the hashed password.
	 */
	public function validatePassword(string $password = '', string $confirmationPassword = '') : string
	{
		//
		// If password is empty, switch to the one provided in $user object. Return an empty string on failure.
		//
		if(empty($password))
		{
			$password = $this->user->getPassword();

			if(empty($password))
			{
				return '';
			}
		}

		//
		// The same principle is applied here as in the previous example.
		//
		if(empty($confirmationPassword))
		{
			$confirmationPassword = $this->user->getConfirmationPassword();

			if(empty($confirmationPassword))
			{
				return '';
			}
		}

		//
		// Return an empty string if passwords are mismatched.
		//
		if($password != $confirmationPassword)
		{
			return '';
		}

		//
		// Given password mustn't exceed the current limits.
		//
		if(strlen($password) < DATA_USER_PASSWORD_MIN_LENGTH || strlen($password) > DATA_USER_PASSWORD_MAX_LENGTH)
		{
			return '';
		}

		return password_hash($password, PASSWORD_DEFAULT);
	}
	
	/**
	 * Sign user in to the system.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function signIn() : bool
	{
		try
		{
			$result = $this->doSignIn();

			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';
		}

		return false;
	}
		
	/**
	 * Sign user out of the system.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 */
	public function signOut(string $username = '', string $email = '', string $password = '') : bool
	{
		if(empty($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_EMAIL]) && 
		empty($_SESSION[SESSION_VAR_NAME_USER_PASSWORD]) || empty($username) && empty($email) && empty($password))
		{
			header('Location: ' . SITE_ROOT);

			return false;
		}

		$_SESSION[SESSION_VAR_NAME_USER_NAME] = '';
		$_SESSION[SESSION_VAR_NAME_USER_EMAIL] = '';
		$_SESSION[SESSION_VAR_NAME_USER_PASSWORD] = '';

		header('Location: ' . SITE_ROOT);

		return true;
	}
	
	/**
	 * Get HTML header.
	 *
	 * @return string HTML header with user data when signed-in and a default one without user data.
	 */
	public function getHeader() : string
	{
		if(isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && !empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return '<a href="' . SITE_ROOT . USER_UPDATE_PAGE_PATH . '">' . $_SESSION[SESSION_VAR_NAME_USER_NAME] . '</a>
			<a href="' . USER_SIGNOUT_PATH . '">Sign out</a>';
		}

		return '<a href="' . USER_SIGNUP_PAGE_PATH . '">Sign up</a>
		<a href="' . USER_SIGNIN_PAGE_PATH . '">Sign in</a>';
	}
	
	/**
	 * Get a user signup form.
	 *
	 * @return string Signup form on success and an empty string on failure.
	 */
	public function getSignupForm() : string
	{
		$form = '<form action="' . USER_SIGNUP_PATH . '" method="post">
		<label for="' . SIGNUP_USER_NAME_FIELD_NAME . '">Username:</label><br>
		<input type="text" id="' . SIGNUP_USER_NAME_FIELD_NAME . '" name="' . SIGNUP_USER_NAME_FIELD_NAME . '"><br>

		<label for="' . SIGNUP_EMAIL_FIELD_NAME . '">E-mail:</label><br>
		<input type="email" id="' . SIGNUP_EMAIL_FIELD_NAME . '" name="' . SIGNUP_EMAIL_FIELD_NAME . '"><br>
		
		<label for="' . SIGNUP_PASSWORD_FIELD_NAME . '">Password:</label><br>
		<input type="password" id="' . SIGNUP_PASSWORD_FIELD_NAME . '" name="' . SIGNUP_PASSWORD_FIELD_NAME . '"><br>
	
		<label for="' . SIGNUP_CONF_PASSWORD_FIELD_NAME . '">Confirm Password:</label><br>
		<input type="password" id="' . SIGNUP_CONF_PASSWORD_FIELD_NAME . '" name="' . 
		SIGNUP_CONF_PASSWORD_FIELD_NAME . '"><br>
	
		<button type="submit" value="Sign up" id="submission-button">Sign up</button>
		</form>';

		//
		// Signed-in user is prohibited from creating a new account.
		//
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) || empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return $form;
		}
		
		//
		// Return to the home page.
		//
		header('Location: ' . SITE_ROOT);

		return '';
	}
	
	/**
	 * Get update form.
	 *
	 * @return string User update form on success and an amepty string on failure.
	 */
	public function getUpdateForm() : string
	{
		$form = '<form action="' . USER_UPDATE_PATH . '" method="post">
		<label for="' . SIGNUP_USER_NAME_FIELD_NAME . '">Username:</label><br>
		input type="text" id="' . SIGNUP_USER_NAME_FIELD_NAME . '" name="' . 
		SIGNUP_USER_NAME_FIELD_NAME . '" value=' . $_SESSION[SESSION_VAR_NAME_USER_NAME] . '><br>

		<label for="' . SIGNUP_EMAIL_FIELD_NAME . '">E-mail:</label><br>
		input type="email" id="' . SIGNUP_EMAIL_FIELD_NAME . '" name="' . 
		SIGNUP_EMAIL_FIELD_NAME . '" value=' . $_SESSION[SESSION_VAR_NAME_USER_EMAIL] . '><br>

		label for="' . SIGNUP_PASSWORD_FIELD_NAME . '">Old Password:</label><br>
		input type="password" id="' . SIGNUP_PASSWORD_FIELD_NAME . '" name="' . SIGNUP_PASSWORD_FIELD_NAME . '"><br>

		<label for="' . SIGNUP_CONF_PASSWORD_FIELD_NAME . '">New Password:</label><br>
		input type="password" id="' . SIGNUP_CONF_PASSWORD_FIELD_NAME . '" name="' .
		SIGNUP_CONF_PASSWORD_FIELD_NAME . '"><br>

		input type="submit" value="Update" id="submission-button">
		</form>';

		//
		// User must be signed-in first in order to update their data.
		//
		if(isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && !empty($_SESSION[SESSION_VAR_NAME_USER_NAME]) && 
		isset($_SESSION[SESSION_VAR_NAME_USER_EMAIL]) && !empty($_SESSION[SESSION_VAR_NAME_USER_EMAIL]))
		{
			return $form;
		}

		//
		// Return to the home page.
		//
		header('Location: ' . SITE_ROOT);

		return '';
	}
	
	/**
	 * Get a user signin form.
	 *
	 * @return string Signin form on success and an empty string on failure.
	 */
	public function getSigninForm() : string
	{
		$form = '<form action="' . USER_SIGNIN_PATH . '" method="post">
		<label for="' . SIGNUP_EMAIL_FIELD_NAME . '">Username:</label><br>
		<input type="text" id="' . SIGNUP_EMAIL_FIELD_NAME . '" name="' . SIGNUP_EMAIL_FIELD_NAME . '"><br>

		<label for="' . SIGNUP_PASSWORD_FIELD_NAME . '">Password:</label><br>
		<input type="password" id="' . SIGNUP_PASSWORD_FIELD_NAME . '" name="' . SIGNUP_PASSWORD_FIELD_NAME . '" ><br>

		<button type="submit" value="Sign in" id="submission-button">Sign in</button>
		</form>';

		//
		// Signed-in user is prohibited from signing in again (that doesn't make any sense).
		//
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) || empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return $form;
		}

		//
		// Return to the home page.
		//
		header('Location: ' . SITE_ROOT);

		return '';
	}

	/**
	 * Execute specific user requests.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 */
	public function run() : bool
	{
		$username = '';
		$email = '';
		$password = '';

		if(isset($_POST[SIGNUP_USER_NAME_FIELD_NAME]) && !empty($_POST[SIGNUP_USER_NAME_FIELD_NAME]) || 
		isset($_POST[SIGNUP_EMAIL_FIELD_NAME]) && !empty($_POST[SIGNUP_EMAIL_FIELD_NAME]) || 
		isset($_POST[SIGNUP_PASSWORD_FIELD_NAME]) && !empty($_POST[SIGNUP_PASSWORD_FIELD_NAME] || 
		isset($_POST[SIGNUP_CONF_PASSWORD_FIELD_NAME]) && !empty($_POST[SIGNUP_CONF_PASSWORD_FIELD_NAME])))
		{
			//
			// Use the following attributes when signing up.
			//
			if(!isset($_GET[ACTION_NAME_USER_SIGNIN]))
			{
				$username = $_POST[SIGNUP_USER_NAME_FIELD_NAME];
				$confirmationPassword = $_POST[SIGNUP_CONF_PASSWORD_FIELD_NAME];
			}

			$email = $_POST[SIGNUP_EMAIL_FIELD_NAME];
			$password = $_POST[SIGNUP_PASSWORD_FIELD_NAME];
		}
		elseif(isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && !empty($_SESSION[SESSION_VAR_NAME_USER_NAME]) || 
		isset($_SESSION[SESSION_VAR_NAME_USER_EMAIL]) && !empty($_SESSION[SESSION_VAR_NAME_USER_EMAIL]) || 
		isset($_SESSION[SESSION_VAR_NAME_USER_PASSWORD]) && !empty($_SESSION[SESSION_VAR_NAME_USER_PASSWORD]))
		{
			$username = $_SESSION[SESSION_VAR_NAME_USER_NAME];
			$email = $_SESSION[SESSION_VAR_NAME_USER_EMAIL];
			$password = $_SESSION[SESSION_VAR_NAME_USER_PASSWORD];
		}
		else
		{
			return false;
		}

		//
		// Use the following for user sign-up.
		//
		if(isset($_GET[ACTION_NAME_USER_SIGNUP]))
		{
			$userController = new UserController(new User(
				$username,
				$email,
				$password,
				$confirmationPassword)
			);
		}
		//
		// And this one for sign-in.
		//
		else
		{
			$userController = new UserController(new User(
				$username,
				$email,
				$password)
			);
		}

		if(isset($_GET[ACTION_NAME_USER_SIGNUP])) 
		{
			$userController->doCreate();
			header('Location: ' . USER_SIGNIN_PAGE_PATH);
		}

		if(isset($_GET[ACTION_NAME_USER_UPDATE])) 
		{
			$userController->update(
				$_SESSION[SESSION_VAR_NAME_USER_NAME]
			);
			header('Location: ' . USER_UPDATE_PAGE_PATH);
		}

		if(isset($_GET[ACTION_NAME_USER_REMOVAL])) {
			$userController->delete(
				$_SESSION[SESSION_VAR_NAME_USER_NAME]
			);
			$userController->signOut();
			header('Location: ' . SITE_ROOT);
		}

		if(isset($_GET[ACTION_NAME_USER_SIGNIN])) 
		{
			$userController->signIn();
			header('Location: ' . SITE_ROOT);
		}

		if(isset($_GET[ACTION_NAME_USER_SIGNOUT])) 
		{
			$userController = new UserController();

			$userController->signOut();
			header('Location: ' . SITE_ROOT);
		}

		return true;
	}
}

$userController = new UserController();
$userController->run();

?>
