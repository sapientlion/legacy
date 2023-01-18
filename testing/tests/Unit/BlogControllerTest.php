<?php


namespace Tests\Unit;

require_once(__DIR__ . '../../../../config.php');
require_once(SITE_ROOT . '/core/controllers/blog_controller.php');
require_once(SITE_ROOT . '/core/controllers/user_controller.php');
require_once(SITE_ROOT . '/core/models/post.php');
require_once(SITE_ROOT . '/core/models/user.php');

use Tests\Support\UnitTester;
use BlogController;
use UserController;
use Post;
use User;

class BlogControllerTest extends \Codeception\Test\Unit
{

	private Post $post;
    protected UnitTester $tester;

    protected function _before()
    {
		$userController = new UserController(
			new User('SapientLion', 'hello@world.org', '1234567890', '1234567890'));
			
		$userController->create();
    }

    // tests
	public function testPostCreation()
	{
		$blogController = new BlogController(
			new Post('Hello, World!', 'SapientLion', 'Hello, World?'));
		$result = $blogController->create();
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testPostUpdate()
	{
		$blogController = new BlogController(
			new Post('Hello, World?', 'SapientLion', 'Hello, World!', 1));
		$result = $blogController->update(1);
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testPostRemoval()
	{
		$blogController = new BlogController(
			new Post('Hello, World?', 'SapientLion', 'Hello, World!', 1));
		$result = $blogController->delete(1);
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}
}
