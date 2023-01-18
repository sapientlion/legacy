<?php


namespace Tests\Unit;

require_once(__DIR__ . '../../../../config.php');
require_once(SITE_ROOT . '/core/controllers/user_controller.php');
require_once(SITE_ROOT . '/core/controllers/blog_controller.php');
require_once(SITE_ROOT . '/core/controllers/comment_controller.php');
require_once(SITE_ROOT . '/core/models/user.php');
require_once(SITE_ROOT . '/core/models/post.php');
require_once(SITE_ROOT . '/core/models/comment.php');

use Tests\Support\UnitTester;
use User;
use UserController;
use Post;
use BlogController;
use Comment;
use CommentController;

class CommentControllerTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    protected function _before()
    {
		$userController = new UserController(
			new User('SapientLion', 'hello@world.org', '1234567890', '1234567890'));
			
		$userController->create();

		$blogController = new BlogController();

		$blogController->define(new Post('Hello, World!', 'SapientLion', 'Hello, World?'));
		$blogController->create();
    }

    // tests
	public function testCommentCreation()
	{
		$commentController = new CommentController(
			new Comment(1, 'SapientLion', 'Hello, World?'));
		$result = $commentController->create();
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testCommentUpdate()
	{
		$commentController = new CommentController(
			new Comment(1, 'SapientLion', 'Hello, World!', 1));
		$result = $commentController->update(1);
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}

	public function testCommentRemoval()
	{
		$commentController = new CommentController(
			new Comment(1, 'SapientLion', 'Hello, World!', 1));
		$result = $commentController->delete(1);
		
		$this->assertIsBool($result);
		$this->assertTrue($result);
	}
}
