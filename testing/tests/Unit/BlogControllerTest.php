<?php


namespace Tests\Unit;

require_once(__DIR__ . '../../../../config.php');
require_once(SITE_ROOT . '/core/controllers/blog_controller.php');
require_once(SITE_ROOT . '/core/controllers/user_controller.php');
require_once(SITE_ROOT . '/core/models/blog_post.php');
require_once(SITE_ROOT . '/core/models/user.php');

use Tests\Support\UnitTester;
use BlogController;
use UserController;
use BlogPost;
use User;

class BlogControllerTest extends \Codeception\Test\Unit
{

	private BlogPost $post;
    protected UnitTester $tester;

    protected function _before()
    {
		$_SESSION['CanCreateBlogPosts'] = '1';
		$_SESSION['CanReadBlogPosts'] = '1';
		$_SESSION['CanUpdateBlogPosts'] = '1';
		$_SESSION['CanDeleteBlogPosts'] = '1';

		$userController = new UserController(
			new User('SapientLion', 'hello@world.org', '1234567890', '1234567890'));
			
		$userController->create();
    }

    // tests
	public function testPostCreation()
	{
		$blogController = new BlogController(
			new BlogPost('Hello, World!', 'SapientLion', 'Hello, World?'));
		$result = $blogController->create();
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testPostUpdate()
	{
		$blogController = new BlogController(
			new BlogPost('Hello, World?', 'SapientLion', 'Hello, World!', 1));
		$result = $blogController->update(1);
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testPostRemoval()
	{
		$blogController = new BlogController(
			new BlogPost('Hello, World?', 'SapientLion', 'Hello, World!', 1));
		$result = $blogController->delete(1);
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testBlogPostDataValidation()
	{
		$blogController = new BlogController();

		$title = 'This is a test';

		for($index = 0; $index < 11; $index++)
		{
			$title .= $title;
		}

		//
		// Failure: title length is greater than given limit.
		//
		$result = $blogController->validate(
			new BlogPost($title, 'SapientLion', 'See?'));

		$this->assertIsBool($result);
		$this->assertEquals($result, false);

		$title = 'SapientLion';

		for($index = 0; $index < 11; $index++)
		{
			$title .= $title;
		}

		//
		// Failure: author length is greater than given limit.
		//
		$result = $blogController->validate(
			new BlogPost('This is a test', $title, 'See?'));

		$this->assertIsBool($result);
		$this->assertEquals($result, false);

		//
		// Success.
		//
		$result = $blogController->validate(
			new BlogPost('This is a test', 'SapientLion', 'See?'));

		$this->assertEquals($result, true);
	}

	public function testBlogPostNumberFetching()
	{
		$blogController = new BlogController(
			new BlogPost('That Dark Side of the Luna', 'SapientLion', 
			'I wonder if someone`s still up there to play a song for me.'));
			
		$blogController->create();

		$blogController = new BlogController(
			new BlogPost('The Bell That Divides...', 'SapientLion', 'I have high hopes for this project.'));
			
		$blogController->create();

		$blogController = new BlogController(
			new BlogPost('Infinite River', 'SapientLion', 'No comment.'));
			
		$blogController->create();

		$result = $blogController->getRowNum();

		//
		// Success.
		//
		$this->assertGreaterThan(0, $result);
		$this->assertEquals($result, 3);
	}
}
