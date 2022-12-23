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

	private function checkUserName() : int
	{
		$stmt = $this->dbh->prepare("SELECT username FROM user");
		$stmt->execute();
		$result = $stmt->fetchAll();
		$result = count($result);

		return $result;
	}

	private function checkEmail() : int
	{
		$stmt = $this->dbh->prepare("SELECT email FROM user");
		$stmt->execute();
		$result = $stmt->fetchAll();
		$result = count($result);

		return $result;
	}
	
	/**
	 * A method wrapper which combines both checkUserName() and checkEmail() methods into one. It is used for searching of
	 * preceding data entries (by username or by email) in DB for duplicate prevention.
	 *
	 * @return int total number of existing user accounts that have identical credentials.
	 */
	private function check() : int
	{
		$num_of_rows = $this->checkUserName();

		if($num_of_rows != 0)
		{
			return $num_of_rows;
		}

		$num_of_rows = $this->checkEmail();

		if($num_of_rows != 0)
		{
			return $num_of_rows;
		}

		return 0;
	}
		
	/**
	 * Create a new user account entry in database.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doCreate() : bool
	{
		if($this->check() != 0)
		{
			return false;
		}

		$stmt = $this->dbh->prepare("INSERT INTO `user` (`username`, `email`, `password`) VALUES (':username', ':email', ':password')");

		$stmt->bindParam(':username', $this->user->getUsername());
		$stmt->bindParam(':email', $this->user->getEmail());
		$stmt->bindParam(':password', $this->user->getPass());
		
		$result = $stmt->execute();

		return $result;
	}
	
	/**
	 * Update preceding user account by supplying it with fresh data.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doUpdate() : bool
	{
		if($this->check() !== 0)
		{
			return false;
		}

		$username = $this->user->getUsername();
		$email = $this->user->getEmail();
		$password = $this->user->getPass();

		$stmt = $this->dbh->prepare("UPDATE user SET 
			username = " . $username . 
			" email = " . $email . 
			" password = " . $password . "WHERE username = " . $username);

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

		return false;
	}
	
	/**
	 * Remove user account from database.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doDelete() : bool
	{
		if($this->check() != 0)
		{
			return false;
		}

		$stmt = $this->dbh->prepare("DELETE FROM user WHERE user = " . $this->user->getUsername());
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
		if($this->checkUserName() === 1)
		{
			$stmt = $this->dbh->prepare("SELECT password FROM user WHERE username = " . $this->user->getUsername());

			$stmt->execute();

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			//
			// Given password might be different than the one saved in database.
			//
			if($this->user->getPass() !== $result)
			{
				return false;
			}

			$stmt = $this->dbh->prepare("SELECT username, email, password FROM user WHERE username = " . $this->user->getUsername());

			$stmt->execute();
			$this->set($stmt);

			return true;
		}
		
		//
		// Sign user in when email address is supplied via respective form input field.
		//
		if($this->checkEmail() === 1)
		{
			$stmt = $this->dbh->prepare("SELECT password FROM user WHERE email = " . $this->user->getEmail());
			$stmt->execute();

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			//
			// Given password might be different than the one saved in database.
			//
			if($this->user->getPass() !== $result)
			{
				return false;
			}

			$stmt = $this->dbh->prepare("SELECT username, email, password FROM user WHERE email = " . $this->user->getEmail());

			$stmt->execute();
			$this->set($stmt);

			return true;
		}

		return false;
	}

	public function __construct()
	{
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

	public function define(User $user) : User
	{
		return $this->user = $user;
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
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function update() : bool
	{
		try
		{
			$result = $this->doUpdate();

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
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function delete() : bool
	{
		try
		{
			$result = $this->doDelete();

			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';

			return false;
		}
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
			$userController->update();
			header('Location: ../../update.php');
		}

		if(isset($_GET['delete'])) {
			$userController->delete();
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
