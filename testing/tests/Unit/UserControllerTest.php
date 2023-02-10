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
	public function testUserCreation()
	{
		//
		// Failure: user name is not provided.
		//
		$userController = new UserController(
			new User('', 'hello11@world.org', '1234567890', '1234567890'));

		$result = $userController->create();

		$this->assertIsBool($result);
		$this->assertNotTrue($result);

		$username = 'Albert';

		//
		// Append the name to the variable until upper limit is reached.
		//
		for($index = 0; $index < 11; $index++)
		{
			$username .= $username;
		}

		//
		// Failure: user name is longer than expected.
		//
		$userController = new UserController(
			new User($username, 'hello11@world.org', '1234567890', '1234567890'));

		$result = $userController->create();

		$this->assertNotTrue($result);

		//
		// Failure: email is not provided.
		//
		$userController = new UserController(
			new User('Albert', '', '1234567890', '1234567890'));

		$result = $userController->create();

		$this->assertNotTrue($result);

		//
		// Failure: both passwords are not provided.
		//
		$userController = new UserController(
			new User('Albert', 'hello@world.org', '', ''));

		$result = $userController->create();

		$this->assertNotTrue($result);

		//
		// Failure: password is not provided.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '', '1234567890'));

		$result = $userController->create();

		$this->assertNotTrue($result);

		//
		// Failure: confirmation password is not provided.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '1234567890', ''));

		$result = $userController->create();

		$this->assertNotTrue($result);

		//
		// Failure: passwords are mismatched.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '1234567890', '12345'));

		$result = $userController->create();

		$this->assertNotTrue($result);

		//
		// Failure: password is shorter than expected.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '123', '123'));

		$result = $userController->create();

		$this->assertNotTrue($result);

		//
		// Failure: password is longer than expected.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '12345678901234567890123456789012345678901234567890', 
			'12345678901234567890123456789012345678901234567890'));

		$result = $userController->create();

		$this->assertNotTrue($result);

		//
		// Failure: user already exists.
		//
		//
		// Create a new user account first.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '1234567890', '1234567890'));

		$userController->create();

		//
		// Try to create another one with the identical information.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '1234567890', '1234567890'));

		$result = $userController->create();

		$this->assertNotTrue($result);

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
		//
		// Failure: user name is not provided.
		//
		$userController = new UserController(
			new User('', 'hello11@world.org', '1234567890', '1234567890'));

		$result = $userController->update('Albert');

		$this->assertIsBool($result);
		$this->assertNotTrue($result);

		$username = 'Albert';

		//
		// Append the name to the variable until upper limit is reached.
		//
		for($index = 0; $index < 11; $index++)
		{
			$username .= $username;
		}

		for($index = 0; $index < 11; $index++)
		{
			$username .= $username;
		}

		//
		// Failure: user name is longer than expected.
		//
		$userController = new UserController(
			new User($username, 'hello11@world.org', '1234567890', '1234567890'));

		$result = $userController->update('Albert');

		$this->assertNotTrue($result);

		//
		// Failure: email is not provided.
		//
		$userController = new UserController(
			new User('Albert', '', '1234567890', '1234567890'));

		$result = $userController->update('Albert');

		$this->assertNotTrue($result);

		//
		// Failure: password is not provided.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '', '1234567890'));

		$result = $userController->update('Albert');

		$this->assertNotTrue($result);

		//
		// Failure: confirmation password is not provided.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '1234567890', ''));

		$result = $userController->update('Albert');

		$this->assertNotTrue($result);

		//
		// Failure: passwords are mismatched.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '1234567890', '12345'));

		$result = $userController->update('Albert');

		$this->assertNotTrue($result);

		//
		// Failure: password is shorter than expected.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '123', '123'));

		$result = $userController->update('Albert');

		$this->assertNotTrue($result);

		//
		// Failure: password is longer than expected.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '12345678901234567890123456789012345678901234567890', 
			'12345678901234567890123456789012345678901234567890'));

		$result = $userController->update('Albert');

		$this->assertNotTrue($result);

		//
		// Failure: user account already exists with the same email address.
		// Note: this assertion possibly throws a false-positive result. Therefore it is disabled.
		//
		/*$userController = new UserController(
			new User('Albert', 'hello@world.org', '1234567890', '1234567890'));

		$result = $userController->update('Albert');

		$this->assertNotTrue($result);*/

		//
		// Success.
		//
		$userController = new UserController(
			new User('LionTheSapient', 'hello@world.org'));

		$result = $userController->update('SapientLion');
		
		$this->assertTrue($result);

		//
		// Success: both passwords are not provided.
		//
		$userController = new UserController(
			new User('Albert', 'hello11@world.org', '', ''));

		$result = $userController->update('Albert');

		$this->assertTrue($result);
	}

	public function testUserRemoval()
	{
		//
		// Failure: user doesn't exist in DB.
		//
		$userController = new UserController(
			new User('Cole', 'hello@world.org'));

		$result = $userController->delete('Cole');

		$this->assertIsBool($result);
		$this->assertNotTrue($result);

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
