<?php

if(session_status() === PHP_SESSION_NONE)
{
	session_start();
}

require_once(__DIR__ . '/../../config.php');
require_once(SITE_ROOT . '/core/interfaces/iuser_controller.php');
require_once(SITE_ROOT . '/core/models/user.php');

class UserController implements IUserController 
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
		$username_len = strlen($username);

		//
		// Check user name string length.
		//
		if($username_len <= 0 || $username_len > DATA_USER_NAME_LENGTH)
		{
			return false;
		}

		$email = $this->user->getEmail();
		$email_len = strlen($email);

		//
		// Check email string length.
		//
		if($email_len <= 0 || $email_len > DATA_USER_EMAIL_LENGTH)
		{
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
		if(!$this->validate() || $this->tryToFindMatchingEntries() > 0)
		{
			return false;
		}

		$password = $this->validatePassword($this->user->getPassword(), $this->user->getConfirmationPassword());

		if(empty($password))
		{
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
		if(!$this->validate() || $this->tryToFindMatchingEmails() > 0)
		{
			return false;
		}

		$password = $this->user->getPassword();
		$confirmationPassword = $this->user->getConfirmationPassword();

		if(!empty($password) || !empty($confirmationPassword))
		{
			$password = $this->validatePassword();

			if(empty($password))
			{
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
				$_SESSION['UserName'] = $username;
				$_SESSION['Email'] = $email;
				$_SESSION['Password'] = $password;
	
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
			$_SESSION['UserName'] = $username;
			$_SESSION['Email'] = $email;

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
		if($this->tryToFindMatchingUserNames() !== 1)
		{
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
		
		$_SESSION['UserName'] = $result[0];
		$_SESSION['Email'] = $result[1];
		$_SESSION['Password'] = $result[2];

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
				return false;
			}

			$string = "SELECT " . 
			DB_TABLE_USER_NAME . ", " . 
			DB_TABLE_USER_EMAIL . ", " . 
			DB_TABLE_USER_PASSWORD . " FROM " . 
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
				return false;
			}

			//
			// SELECT username, email, password FROM user WHERE email = :email
			//
			$string = "SELECT " . 
			DB_TABLE_USER_NAME . ", " . 
			DB_TABLE_USER_EMAIL . ", " . 
			DB_TABLE_USER_PASSWORD . " FROM " . 
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
		if(empty($_SESSION['UserName']) && empty($_SESSION['Email']) && empty($_SESSION['Password']) || 
		empty($username) && empty($email) && empty($password))
		{
			header('Location: ' . SITE_ROOT);

			return false;
		}

		$_SESSION['UserName'] = '';
		$_SESSION['Email'] = '';
		$_SESSION['Password'] = '';

		header('Location: ' . SITE_ROOT);

		return true;
	}
	
	/**
	 * Generate HTML header and fill it with appropriate child elements.
	 *
	 * @return string HTML header.
	 */
	public function genSiteHeader() : string
	{
		if(isset($_SESSION['UserName']) && !empty($_SESSION['UserName']))
		{
			return '<a href="user_updater.php">' . $_SESSION['UserName'] . '</a>
			<a href="api/controllers/user_controller.php?signout">Sign out</a>';
		}
		else
		{
			return '<a href="user_signup.php">Sign up</a>
			<a href="user_signin.php">Sign in</a>';
		}		
	}

	/**
	 * Run certain methods depending on given $_GET values.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 */
	public function run() : bool
	{
		$user = ['', '', ''];

		//
		// Prevent breakage by checking out whether $_POST and/or $_SESSION variables are set or not.
		//
		if(
			isset($_POST['username']) ||
			isset($_POST['email']) ||
			isset($_POST['password'])
		) 
		{
			$user = [$_POST['username'], $_POST['email'], $_POST['password']];
		}
		elseif (
			isset($_SESSION['UserName']) && !empty($_SESSION['UserName']) ||
			isset($_SESSION['Email']) && !empty($_SESSION['Email']) ||
			isset($_SESSION['Password']) && !empty($_SESSION['Password'])
		) 
		{
			$user = [$_SESSION['UserName'], $_SESSION['Email'], $_SESSION['Password']];
		} 
		else 
		{
			return false;
		}

		$userController = new UserController(new User(
			$user[0],
			$user[1],
			$user[2]
		));

		if(isset($_GET['signup'])) 
		{
			$userController->create();
			header('Location: ../../signIn.php');
		}

		if(isset($_GET['update'])) 
		{
			$userController->update($_SESSION['UserName']);
			header('Location: ../../update.php');
		}

		if(isset($_GET['delete'])) {
			$userController->delete($_SESSION['UserName']);
			$userController->signOut();
			header('Location: ../../index.php');
		}

		if(isset($_GET['signIn'])) 
		{
			$userController->signIn();
			header('Location: ../../index.php');
		}

		if(isset($_GET['signOut'])) 
		{
			$userController = new UserController();

			$userController->signOut();
			header('Location: ../../index.php');
		}

		return true;
	}
}

$userController = new UserController();
$userController->run();

?>
