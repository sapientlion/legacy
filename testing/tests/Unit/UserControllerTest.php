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

	private UserController $userController;
    protected UnitTester $tester;

    protected function _before()
    {
		$this->userController = new UserController(
			new User('SapientLion', 'hello@world.org', '1234567890', '1234567890'));
    }

    // tests
	public function testUserCreation()
	{
		$result = $this->userController->create();
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testUserUpdate()
	{
		$this->userController = new UserController(
			new User('LionTheSapient', 'hello@world.org'));

		$result = $this->userController->update('SapientLion');
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testUserRemoval()
	{
		$this->userController = new UserController(
			new User('LionTheSapient', 'hello@world.org', '1234567890'));

		$result = $this->userController->delete('LionTheSapient');
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}
}
