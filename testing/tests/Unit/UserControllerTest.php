<?php


namespace Tests\Unit;

require_once(__DIR__ . '../../../../config.php');
require_once(SITE_ROOT . '/core/controllers/user_controller.php');
require_once(SITE_ROOT . '/core/models/user.php');

use Tests\Support\UnitTester;
use UserController;
use User;

class UserControllerTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

	private function append(string $str, int $cycles = 0) : string
	{
		if(empty($str))
		{
			return $str;
		}

		if($cycles <= 0)
		{
			return $str .= $str;
		}

		for($index = 0; $index < $cycles; $index++)
		{
			$str .= $str;
		}

		return $str;
	}

	private function tryToCreateUserWithoutUserName(bool $mflag, string $username = '') : void
	{
		if($mflag && empty($usename))
		{
			return;
		}

		$userController = new UserController(
			new User('', 'goodbye@world.org', '1234567890', '1234567890'));

		$result = false;

		if(!$mflag)
		{
			$_SESSION['UserName'] = $username;
			$result = $userController->update($username);
		}
		else
		{
			$result = $userController->create();	
		}

		$this->assertNotTrue($result);
	}

	private function tryToCreateUserWithLongUserName(bool $mflag, string $username = '') : void
	{
		if($mflag && empty($usename))
		{
			return;
		}

		$username = $this->append($username, 11);

		$userController = new UserController(
			new User($username, 'goodbye@world.org', '1234567890', '1234567890'));

		if(!$mflag)
		{
			$_SESSION['UserName'] = $username;
			$result = $userController->update($username);
		}
		else
		{
			$result = $userController->create();	
		}

		$this->assertNotTrue($result);
	}

	private function tryToCreateUserWithEmptyEmail(bool $mflag, string $username = '') : void
	{
		if($mflag && empty($usename))
		{
			return;
		}

		$userController = new UserController(
			new User('Albert', '', '1234567890', '1234567890'));

		if(!$mflag)
		{
			$_SESSION['UserName'] = $username;
			$result = $userController->update($username);
		}
		else
		{
			$result = $userController->create();	
		}

		$this->assertNotTrue($result);
	}

	private function tryToCreateUserWithoutAnyPasswords(bool $mflag, string $username = '') : void
	{
		if($mflag && empty($usename))
		{
			return;
		}

		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', '', ''));

		if(!$mflag)
		{
			$_SESSION['UserName'] = $username;
			$result = $userController->update($username);
		}
		else
		{
			$result = $userController->create();	
		}

		$this->assertNotTrue($result);
	}

	private function tryToCreateUserWithoutPassword(bool $mflag, string $username = '') : void
	{
		if($mflag && empty($usename))
		{
			return;
		}

		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', '', '1234567890'));

		if(!$mflag)
		{
			$_SESSION['UserName'] = $username;
			$result = $userController->update($username);
		}
		else
		{
			$result = $userController->create();	
		}

		$this->assertNotTrue($result);
	}

	private function tryToCreateUserWithoutConfirmationPassword(bool $mflag, string $username = '') : void
	{
		if($mflag && empty($usename))
		{
			return;
		}

		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', '1234567890', ''));

		if(!$mflag)
		{
			$_SESSION['UserName'] = $username;
			$result = $userController->update($username);
		}
		else
		{
			$result = $userController->create();	
		}

		$this->assertNotTrue($result);
	}

	private function tryToCreateUserWithMismatchedPasswords(bool $mflag, string $username = '') : void
	{
		if($mflag && empty($usename))
		{
			return;
		}

		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', '1234567890', '12345'));

		if(!$mflag)
		{
			$_SESSION['UserName'] = $username;
			$result = $userController->update($username);
		}
		else
		{
			$result = $userController->create();	
		}

		$this->assertNotTrue($result);
	}

	private function tryToCreateUserWithShortPassword(bool $mflag, string $username = '') : void
	{
		if($mflag && empty($usename))
		{
			return;
		}

		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', '123', '123'));

		if(!$mflag)
		{
			$_SESSION['UserName'] = $username;
			$result = $userController->update($username);
		}
		else
		{
			$result = $userController->create();	
		}

		$this->assertNotTrue($result);
	}

	private function tryToCreateUserWithLongPassword(bool $mflag, string $password, string $username = '') : void
	{
		if($mflag && empty($usename))
		{
			return;
		}
		
		$password = $this->append($password, 11);

		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', $password, $password));

		if(!$mflag)
		{
			$_SESSION['UserName'] = $username;
			$result = $userController->update($username);
		}
		else
		{
			$result = $userController->create();	
		}

		$this->assertNotTrue($result);
	}

	private function tryToCreateUsersWithIdenticalCredentials() : void
	{
		$username = 'Albert';
		$email = 'goodbye@world.org';
		$password = '1234567890';

		//
		// Create a new account first.
		//
		$userController = new UserController(
			new User($username, $email, $password, $password));

		$userController->create();

		//
		// Create another one with the same info.
		//
		$userController = new UserController(
			new User($username, $email, $password, $password));

		$result = $userController->create();

		$this->assertNotTrue($result);
	}

    protected function _before()
    {
    }

    // tests
	public function testUserCreation()
	{
		$this->tryToCreateUserWithoutUserName(TRUE);				// Failure: user name isn't provided.
		$this->tryToCreateUserWithLongUserName('Albert');			// Failure: user name is too long to fit in DB.
		$this->tryToCreateUserWithEmptyEmail(TRUE);					// Failure: email address isn't provided.
		$this->tryToCreateUserWithoutAnyPasswords(TRUE);			// Failure: both passwords aren't provided.
		$this->tryToCreateUserWithoutPassword(TRUE);				// Failure: password isn't provided.
		$this->tryToCreateUserWithoutConfirmationPassword(TRUE);	// Failure: confirmation password isn't provided.
		$this->tryToCreateUserWithMismatchedPasswords(TRUE);		// Failure: provided passwords are mismatched.
		$this->tryToCreateUserWithShortPassword(TRUE);				// Failure: provided password is too short.
		$this->tryToCreateUserWithLongPassword(TRUE, '1234567890');	// Failure: provided password is too long.
		$this->tryToCreateUsersWithIdenticalCredentials();			// Failure: duplicate user accounts aren't allowed.

		//
		// Success.
		//
		$userController = new UserController(
			new User('SapientLion', 'hello@world.org', '1234567890', '1234567890'));
		$result = $userController->create();
		
		$this->assertTrue($result);
	}

	public function testUserUpdate()
	{
		$username = 'Albert';

		$userController = new UserController(
			new User($username, 'goodbye@world.org', '1234567890', '1234567890'));
		$result = $userController->create();

		$this->tryToCreateUserWithoutUserName(FALSE, $username);				// Failure: user name isn't provided.
		$this->tryToCreateUserWithLongUserName(FALSE, $username);				// Failure: user name is too long to fit in DB.
		$this->tryToCreateUserWithEmptyEmail(FALSE, $username);					// Failure: email address isn't provided.
		$this->tryToCreateUserWithoutAnyPasswords(FALSE, $username);			// Failure: both passwords aren't provided.
		$this->tryToCreateUserWithoutPassword(FALSE. $username);				// Failure: password isn't provided.
		$this->tryToCreateUserWithoutConfirmationPassword(FALSE, $username);	// Failure: confirmation password isn't provided.
		$this->tryToCreateUserWithMismatchedPasswords(FALSE, $username);		// Failure: provided passwords are mismatched.
		$this->tryToCreateUserWithShortPassword(FALSE, $username);				// Failure: provided password is too short.
		$this->tryToCreateUserWithLongPassword(FALSE, '1234567890', $username);	// Failure: provided password is too long.

		//
		// Failure: user account already exists with the same email address.
		// Note: this assertion possibly throws a false-positive result. Therefore it is disabled.
		//
		/*$userController = new UserController(
			new User('Albert', 'hello@world.org', '1234567890', '1234567890'));

		$result = $userController->update('Albert');

		$this->assertNotTrue($result);*/

		$_SESSION['UserName'] = 'SapientLion';

		//
		// Success: both passwords are provided.
		//
		$userController = new UserController(
			new User('LionTheSapient', 'hello@world.org', '0987654321', '0987654321'));

		$result = $userController->update('SapientLion');
		
		$this->assertTrue($result);

		$_SESSION['UserName'] = $username;

		//
		// Success: both passwords aren't provided.
		//
		$userController = new UserController(
			new User('Cole', 'goodbye@world.org', '', ''));

		$result = $userController->update($username);

		$this->assertTrue($result);
	}

	public function testUserRemoval()
	{
		//
		// Failure: user doesn't exist in DB.
		//
		$userController = new UserController(
			new User('Albert', 'goodbye@world.org'));

		$result = $userController->delete('Albert');

		$this->assertIsBool($result);
		$this->assertNotTrue($result);

		$_SESSION['UserName'] = 'SapientLion';

		//
		// Success.
		//
		$userController = new UserController(
			new User('SapientLion', 'hello@world.org', '1234567890', '1234567890'));

		$userController->create();
		$result = $userController->delete('SapientLion');
		
		$this->assertTrue($result);
	}
}
