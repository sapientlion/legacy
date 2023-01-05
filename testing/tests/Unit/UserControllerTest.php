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
	private User $user;
    protected UnitTester $tester;

    protected function _before()
    {
		$this->userController = new UserController();
    }

    // tests
    public function testUserDefinition()
    {
		$this->user = new User('SapientLion', 'hello@world.org', '1234567890');
		
		$result = $this->userController->define($this->user);

		$this->assertIsObject($result);
		$this->assertEquals($result, $this->user);
    }

	public function testUserCreation()
	{
		$this->user = new User('SapientLion', 'hello@world.org', '1234567890');

		$this->userController->define($this->user);

		$result = $this->userController->create();
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testUserUpdate()
	{
		$this->user = new User('LionTheSapient', 'hello@world.org', '1234567890');

		$this->userController->define($this->user);

		$result = $this->userController->update('SapientLion');
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testUserRemoval()
	{
		$this->user = new User('LionTheSapient', 'hello@world.org', '1234567890');

		$this->userController->define($this->user);

		$result = $this->userController->delete('LionTheSapient');
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}
}
