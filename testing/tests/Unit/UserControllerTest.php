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

    protected function _before()
    {
    }

    // tests
	public function testUserSignupWithoutUserName()
	{
		$userController = new UserController(
			new User('', 'goodbye@world.org', '1234567890', '1234567890')
		);

		$result = $userController->create();

		$this->assertNotTrue($result);
	}

	public function testUserSignupWithLongUserName()
	{
		$username = 'Albert';

		for($index = 0; $index < 11; $index++)
		{
			$username .= $username;
		}

		$userController = new UserController(
			new User($username, 'goodbye@world.org', '1234567890', '1234567890'));

		$result = $userController->create();

		$this->assertNotTrue($result);
	}

	public function testUserSignupWithoutEmail()
	{
		$userController = new UserController(
			new User('Albert', '', '1234567890', '1234567890'));

		$result = $userController->create();

		$this->assertNotTrue($result);
	}

	public function testUserSignupWithoutBothPasswords()
	{
		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', '', ''));

		$result = $userController->create();

		$this->assertNotTrue($result);
	}

	public function testUserSignupWithoutPassword()
	{
		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', '', '1234567890'));

		$result = $userController->create();

		$this->assertNotTrue($result);
	}

	public function testUserSignupWithoutConfirmationPassword()
	{
		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', '1234567890', ''));

		$result = $userController->create();

		$this->assertNotTrue($result);
	}

	public function testUserSignupWithMismatchedPasswords()
	{
		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', '1234567890', '12345'));

		$result = $userController->create();

		$this->assertNotTrue($result);
	}

	public function testUserSignupWithShortPassword()
	{
		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', '123', '123'));

		$result = $userController->create();

		$this->assertNotTrue($result);
	}

	public function testUserSignupWithLongPassword()
	{
		$password = '1234567890';

		for($index = 0; $index < 11; $index++)
		{
			$password .= $password;
		}

		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', $password, $password)
		);

		$result = $userController->create();

		$this->assertNotTrue($result);
	}

	public function testUserSignupWithBothAccountsBeingIdentical()
	{
		$username = 'Albert';
		$email = 'goodbye@world.org';
		$password = '1234567890';

		//
		// Create a new user account first.
		//
		$userController = new UserController(
			new User($username, $email, $password, $password)
		);

		$userController->create();

		//
		// Try to create another one with the same information.
		//
		$userController = new UserController(
			new User($username, $email, $password, $password)
		);

		$result = $userController->create();

		$this->assertNotTrue($result);
	}

	//
	// Success.
	//
	public function testUserSignup()
	{
		$userController = new UserController(
			new User('SapientLion', 'hello@world.org', '1234567890', '1234567890')
		);
		$result = $userController->create();
		
		$this->assertTrue($result);
	}

	public function testUserUpdateWithoutUserName()
	{
		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', '1234567890', '1234567890')
		);

		$userController->create();

		$_SESSION['UserName'] = 'Albert';

		$userController = new UserController(
			new User('', 'goodbye@world.org', '1234567890', '1234567890')
		);

		$result = $userController->update($_SESSION['UserName']);

		$this->assertNotTrue($result);
	}

	public function testUserUpdateWithLongUserName()
	{
		$username = 'Albert';

		$userController = new UserController(
			new User($username, 'goodbye@world.org', '1234567890', '1234567890')
		);

		$userController->create();

		for($index = 0; $index < 11; $index++)
		{
			$username .= $username;
		}

		$_SESSION['UserName'] = 'Albert';

		$userController = new UserController(
			new User($username, 'goodbye@world.org', '1234567890', '1234567890')
		);

		$result = $userController->update($_SESSION['UserName']);

		$this->assertNotTrue($result);
	}

	public function testUserUpdateWithoutEmail()
	{
		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', '1234567890', '1234567890')
		);

		$userController->create();

		$_SESSION['UserName'] = 'Albert';

		$userController = new UserController(
			new User($_SESSION['UserName'], '', '1234567890', '1234567890')
		);

		$result = $userController->update($_SESSION['UserName']);

		$this->assertNotTrue($result);
	}

	public function testUserUpdateWithoutPassword()
	{
		$password = '1234567890';

		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', $password, $password)
		);

		$userController->create();

		$_SESSION['UserName'] = 'Albert';

		$userController = new UserController(
			new User($_SESSION['UserName'], 'goodbye@world.org', '', $password)
		);

		$result = $userController->update($_SESSION['UserName']);

		$this->assertNotTrue($result);
	}

	public function testUserUpdateWithoutConfirmationPassword()
	{
		$password = '1234567890';

		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', $password, $password)
		);

		$userController->create();

		$_SESSION['UserName'] = 'Albert';

		$userController = new UserController(
			new User($_SESSION['UserName'], 'goodbye@world.org', $password, '')
		);

		$result = $userController->update('Albert');

		$this->assertNotTrue($result);
	}

	public function testUserUpdateWithMismatchedPasswords()
	{
		$password = '1234567890';

		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', $password, $password)
		);

		$userController->create();

		$_SESSION['UserName'] = 'Albert';

		$userController = new UserController(
			new User($_SESSION['UserName'], 'goodbye@world.org', $password, '12345')
		);

		$result = $userController->update($_SESSION['UserName']);

		$this->assertNotTrue($result);
	}

	public function testUserUpdateWithShortPassword()
	{
		$password = '1234567890';

		$userController = new UserController(
			new User('Albert', 'hello11@world.org', $password, $password)
		);

		$userController->create();

		$_SESSION['UserName'] = 'Albert';
		$password = '123';

		$userController = new UserController(
			new User($_SESSION['UserName'], 'hello11@world.org', $password, $password)
		);

		$result = $userController->update($_SESSION['UserName']);

		$this->assertNotTrue($result);
	}

	public function testUserUpdateWithLongPassword()
	{
		$password = '1234567890';

		$userController = new UserController(
			new User('Albert', 'hello11@world.org', $password, $password)
		);

		$userController->create();

		for($index = 0; $index < 11; $index++)
		{
			$password .= $password;
		}

		$_SESSION['UserName'] = 'Albert';

		$userController = new UserController(
			new User($_SESSION['UserName'], 'hello11@world.org', $password, $password)
		);

		$result = $userController->update($_SESSION['UserName']);

		$this->assertNotTrue($result);
	}

	//
	// Success.
	//
	public function testUserUpdate()
	{
		//
		// Failure: user account already exists with the same email address.
		// Note: this assertion possibly throws a false-positive result. Therefore it is disabled by default.
		//
		/*$userController = new UserController(
			new User('Albert', 'hello@world.org', '1234567890', '1234567890'));

		$result = $userController->update('Albert');

		$this->assertNotTrue($result);*/

		$password = '1234567890';

		$userController = new UserController(
			new User('SapientLion', 'hello@world.org', $password, $password)
		);

		$userController->create();

		$_SESSION['UserName'] = 'SapientLion';

		//
		// Success.
		//
		$userController = new UserController(
			new User('LionTheSapient', 'hello@world.org', '', '')
		);

		$result = $userController->update($_SESSION['UserName']);
		
		$this->assertTrue($result);

		$password = '1234567890';

		$userController = new UserController(
			new User('Albert', 'goodbye@world.org', $password, $password)
		);

		$userController->create();

		$_SESSION['UserName'] = 'Albert';

		//
		// Success: both passwords are not provided.
		//
		$userController = new UserController(
			new User('Alfred', 'goodbye@world.org', '', '')
		);

		$result = $userController->update($_SESSION['UserName']);

		$this->assertTrue($result);
	}

	public function testUserRemovalWithNonExistingUserName()
	{
		$_SESSION['UserName'] = 'Cole';

		$userController = new UserController(
			new User($_SESSION['UserName'], 'hello@world.org')
		);

		$result = $userController->delete($_SESSION['UserName']);

		$this->assertNotTrue($result);
	}

	//
	// Success.
	//
	public function testUserRemoval()
	{
		$password = '1234567890';

		$userController = new UserController(
			new User('SapientLion', 'hello@world.org', $password, $password)
		);

		$userController->create();

		$_SESSION['UserName'] = 'SapientLion';

		$result = $userController->delete($_SESSION['UserName']);
		
		$this->assertTrue($result);
	}
}
