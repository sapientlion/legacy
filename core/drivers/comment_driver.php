<?php

if(session_status() === PHP_SESSION_NONE) 
{
	session_start();
}

require_once(__DIR__ . '../../../config.php');
require_once(SITE_ROOT . '/core/controllers/comment_controller.php');
require_once(SITE_ROOT . '/core/controllers/system_controller.php');
require_once(SITE_ROOT . '/core/interfaces/icomment_driver.php');
require_once(SITE_ROOT . '/core/settings/get.php');
require_once(SITE_ROOT . '/core/settings/input.php');
require_once(SITE_ROOT . '/core/settings/paths.php');
require_once(SITE_ROOT . '/core/settings/session.php');

class CommentDriver extends SystemController implements ICommentDriver
{
	/**
	 * Check prerequisites for blog post creation.
	 *
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function checkCreateRequest() : bool
	{
		if(!isset($_POST[COMMENT_POST_ID_FIELD_NAME]) && empty($_POST[COMMENT_POST_ID_FIELD_NAME]))
		{
			return false;
		}

		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return false;
		}

		if(!isset($_POST[COMMENT_CONTENT_FIELD_NAME]) && empty($_POST[COMMENT_CONTENT_FIELD_NAME]))
		{
			return false;
		}

		return true;
	}
	
	/**
	 * Check prerequisites for blog post update.
	 *
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function checkUpdateRequest() : bool
	{
		if(!isset($_POST[COMMENT_ID_FIELD_NAME]) && empty($_POST[COMMENT_ID_FIELD_NAME]))
		{
			return false;
		}

		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return false;
		}

		if(!isset($_POST[COMMENT_CONTENT_FIELD_NAME]) && empty($_POST[COMMENT_CONTENT_FIELD_NAME]))
		{
			return false;
		}

		return true;
	}
	
	/**
	 * Check prerequisites for blog post removal.
	 *
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function checkDeleteRequest() : bool
	{
		if(!isset($_POST[COMMENT_ID_FIELD_NAME]) && empty($_POST[COMMENT_ID_FIELD_NAME]))
		{
			return false;
		}

		return true;
	}
	
	/**
	 * Create a new blog post.
	 *
	 * @param  array $commentData list of blog post information to insert into database.
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function create(array $commentData) : bool
	{
		$blogPostController = new CommentController(
			new Comment(
				(int)($commentData[COMMENT_POST_ID_FIELD_NAME]),
				$commentData[COMMENT_AUTHOR_FIELD_NAME],
				$commentData[COMMENT_CONTENT_FIELD_NAME]
				)
		);

		$result = $blogPostController->create();

		return $result;
	}
	
	/**
	 * Update preceding blog post.
	 *
	 * @param  array $commentData list of blog post data to insert into database.
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function update(array $commentData) : bool
	{
		$blogPostController = new CommentController(
			new Comment(
				(int)($commentData[COMMENT_POST_ID_FIELD_NAME]),
				$commentData[COMMENT_AUTHOR_FIELD_NAME],
				$commentData[COMMENT_CONTENT_FIELD_NAME],
				(int)($commentData[COMMENT_ID_FIELD_NAME])
				)
		);

		$result = $blogPostController->update(
			$commentData[COMMENT_ID_FIELD_NAME]
		);

		return $result;
	}
	
	/**
	 * Delete a blog post from database.
	 *
	 * @param  array $commentData list of blog post data to insert into database.
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function delete(array $commentData) : bool
	{
		$blogPostController = new CommentController(
			new Comment(-1,
			'',
			'',
			$_POST[COMMENT_ID_FIELD_NAME]
			)
		);

		$result = $blogPostController->delete();

		return $result;
	}
	
	/**
	 * Driver method. Activate specific system component requested by a user.
	 *
	 * @return bool TRUE on success and FALSE on failure.
	 */
	public function run() : bool
	{
		if(isset($_POST[COMMENT_SUBMIT_BUTTON_NAME]) && $_POST[COMMENT_SUBMIT_BUTTON_NAME] === ACTION_NAME_COMMENT_CREATION)
		{
			if(!$this->checkCreateRequest())
			{
				return false;
			}

			$commentData = [
				COMMENT_POST_ID_FIELD_NAME => $_POST[COMMENT_POST_ID_FIELD_NAME],
				COMMENT_AUTHOR_FIELD_NAME => $_SESSION[SESSION_VAR_NAME_USER_NAME],
				COMMENT_CONTENT_FIELD_NAME => $_POST[COMMENT_CONTENT_FIELD_NAME],
			];

			$result = $this->create($commentData);

			header(
				'Location: ' . BLOG_VIEW_PAGE_PATH . '?post=' . $_GET[GET_VAR_NAME_BLOG_POST]
			);

			return $result;
		}

		if(isset($_POST[COMMENT_SUBMIT_BUTTON_NAME]) && $_POST[COMMENT_SUBMIT_BUTTON_NAME] === ACTION_NAME_COMMENT_UPDATE)
		{
			if(!$this->checkUpdateRequest())
			{
				return false;
			}

			$commentData = [
				COMMENT_POST_ID_FIELD_NAME => $_POST[COMMENT_POST_ID_FIELD_NAME],
				COMMENT_AUTHOR_FIELD_NAME => $_POST[COMMENT_AUTHOR_FIELD_NAME],
				COMMENT_CONTENT_FIELD_NAME => $_POST[COMMENT_CONTENT_FIELD_NAME],
				COMMENT_ID_FIELD_NAME => $_POST[COMMENT_ID_FIELD_NAME]
			];

			$result = $this->update($commentData);

			header(
				'Location: ' . BLOG_VIEW_PAGE_PATH . '?post=' . $_GET[GET_VAR_NAME_BLOG_POST]
			);

			return $result;
		}

		if(isset($_POST[COMMENT_SUBMIT_BUTTON_NAME]) && $_POST[COMMENT_SUBMIT_BUTTON_NAME] === ACTION_NAME_COMMENT_REMOVAL)
		{
			if(!$this->checkDeleteRequest())
			{
				return false;
			}

			$commentData = [
				COMMENT_ID_FIELD_NAME => $_POST[COMMENT_ID_FIELD_NAME],
			];

			$result = $this->delete($commentData);

			header(
				'Location: ' . BLOG_VIEW_PAGE_PATH . '?post=' . $_GET[GET_VAR_NAME_BLOG_POST]
			);

			return $result;
		}

		return false;
	}
}

$commentDriver = new CommentDriver();

$commentDriver->run();

?>
