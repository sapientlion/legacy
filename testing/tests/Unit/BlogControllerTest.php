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

	private BlogController $blogController;
	private Post $post;
    protected UnitTester $tester;

    protected function _before()
    {
		$user = new User('SapientLion', 'hello@world.org', '1234567890');
		$userController = new UserController(); 

		$userController->define($user);
		$userController->create();

		$this->blogController = new BlogController();
    }

    // tests
    public function testPostDefinition()
    {
		$this->post = new Post('Hello, World!', 'SapientLion', 'Hello, World?');
		
		$result = $this->blogController->define($this->post);

		$this->assertIsObject($result);
		$this->assertEquals($result, $this->post);
    }

	public function testPostCreation()
	{
		$this->post = new Post('Hello, World!', 'SapientLion', 'Hello, World?');

		$this->blogController->define($this->post);

		$result = $this->blogController->create();
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testPostUpdate()
	{
		$this->post = new Post('Hello, World?', 'SapientLion', 'Hello, World!', 1);

		$this->blogController->define($this->post);

		$result = $this->blogController->update(1);
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testPostRemoval()
	{
		$this->post = new Post('Hello, World?', 'SapientLion', 'Hello, World!', 1);

		$this->blogController->define($this->post);

		$result = $this->blogController->delete(1);
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}
}
