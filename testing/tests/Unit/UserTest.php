<?php


namespace Tests\Unit;

require_once(__DIR__ . '../../../../config.php');
require_once(SITE_ROOT . '/core/models/user.php');

use Tests\Support\UnitTester;
use User;

class UserTest extends \Codeception\Test\Unit
{

	private User $user;
    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function testUserCreation()
    {
		$username = 'SapientLion';
		$email = 'hello@world.org';
		$password = '1234567890';

		$this->user = new User($username, $email, $password);

		$result = $this->user->getUsername();

		$this->assertIsString($result);
		$this->assertEquals($result, $username);

		$result = $this->user->getEmail();

		$this->assertIsString($result);
		$this->assertEquals($result, $email);

		$result = $this->user->getPass();

		$this->assertIsString($result);
		//
		// Passwords shouldn't be equal because password hashing is used during object initialization.
		//
		$this->assertNotEquals($result, $password);
    }
}
