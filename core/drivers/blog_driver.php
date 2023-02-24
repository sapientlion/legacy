<?php

require_once(__DIR__ . '../../../config.php');
require_once(SITE_ROOT . '/core/interfaces/iblog_driver.php');
require_once(SITE_ROOT . '/core/settings/get.php');
require_once(SITE_ROOT . '/core/settings/input.php');
require_once(SITE_ROOT . '/core/settings/paths.php');
require_once(SITE_ROOT . '/core/settings/session.php');

class BlogDriver implements IBlogDriver
{
	/**
	 * Check prerequisites for user account creation.
	 *
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function checkCreateRequest() : bool
	{
		if(!isset($_POST[BLOG_POST_TITLE_FIELD_NAME]) && empty($_POST[BLOG_POST_TITLE_FIELD_NAME]))
		{
			return false;
		}

		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return false;
		}

		if(!isset($_POST[BLOG_POST_CONTENT_FIELD_NAME]) && empty($_POST[BLOG_POST_CONTENT_FIELD_NAME]))
		{
			return false;
		}

		return true;
	}
	
	/**
	 * Check prerequisites for user account update.
	 *
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function checkUpdateRequest() : bool
	{
		if(!isset($_POST[BLOG_POST_ID_FIELD_NAME]) && empty($_POST[BLOG_POST_ID_FIELD_NAME]))
		{
			return false;
		}

		if(!isset($_POST[BLOG_POST_TITLE_FIELD_NAME]) && empty($_POST[BLOG_POST_TITLE_FIELD_NAME]))
		{
			return false;
		}

		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return false;
		}

		if(!isset($_POST[BLOG_POST_CONTENT_FIELD_NAME]) && empty($_POST[BLOG_POST_CONTENT_FIELD_NAME]))
		{
			return false;
		}

		return true;
	}
	
	/**
	 * Check prerequisites for user account termination.
	 *
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function checkDeleteRequest() : bool
	{
		if(!isset($_POST[BLOG_POST_ID_FIELD_NAME]) && empty($_POST[BLOG_POST_ID_FIELD_NAME]))
		{
			return false;
		}

		return true;
	}
	
	/**
	 * Create a new user account.
	 *
	 * @param  array $postData list of user credentials to insert into database.
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function create(array $postData) : bool
	{
		$blogPostController = new BlogController(new BlogPost(
			$postData[BLOG_POST_TITLE_FIELD_NAME],
			$postData[BLOG_POST_AUTHOR_FIELD_NAME],
			$postData[BLOG_POST_CONTENT_FIELD_NAME])
		);

		$result = $blogPostController->create();

		return $result;
	}
	
	/**
	 * Update preceding user account.
	 *
	 * @param  array $postData list of user credentials to insert into database.
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function update(array $postData) : bool
	{
		$blogPostController = new BlogController(new BlogPost(
			$postData[BLOG_POST_TITLE_FIELD_NAME],
			$postData[BLOG_POST_AUTHOR_FIELD_NAME],
			$postData[BLOG_POST_CONTENT_FIELD_NAME])
		);

		$result = $blogPostController->update(
			$postData[BLOG_POST_ID_FIELD_NAME]
		);

		return $result;
	}
	
	/**
	 * delete
	 *
	 * @param  array $postData list of user credentials to insert into database.
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function delete(array $postData) : bool
	{
		$blogPostController = new BlogController(new BlogPost(
			$postData[BLOG_POST_TITLE_FIELD_NAME],
			$postData[BLOG_POST_AUTHOR_FIELD_NAME],
			$postData[BLOG_POST_CONTENT_FIELD_NAME])
		);

		$result = $blogPostController->delete(
			$_POST[BLOG_POST_ID_FIELD_NAME]
		);

		return $result;
	}
	
	/**
	 * Driver method. Activate specific system component requested by a user.
	 *
	 * @return bool TRUE on success and FALSE on failure.
	 */
	public function run() : bool
	{
		if(isset($_GET[ACTION_NAME_BLOG_POST_CREATION]))
		{
			if(!$this->checkCreateRequest())
			{
				return false;
			}

			$postData = [
				BLOG_POST_TITLE_FIELD_NAME => $_POST[BLOG_POST_TITLE_FIELD_NAME],
				BLOG_POST_AUTHOR_FIELD_NAME => $_POST[BLOG_POST_AUTHOR_FIELD_NAME],
				BLOG_POST_CONTENT_FIELD_NAME => $_POST[BLOG_POST_CONTENT_FIELD_NAME],
			];

			$result = $this->create($postData);

			//
			// TODO redirect to the blog post reader page instead.
			//
			header('Location: /index.php');

			return $result;
		}

		if(isset($_GET[ACTION_NAME_BLOG_POST_UPDATE]))
		{
			if(!$this->checkUpdateRequest())
			{
				return false;
			}

			$postData = [
				BLOG_POST_ID_FIELD_NAME => $_POST[BLOG_POST_ID_FIELD_NAME],
				BLOG_POST_TITLE_FIELD_NAME => $_POST[BLOG_POST_TITLE_FIELD_NAME],
				BLOG_POST_AUTHOR_FIELD_NAME => $_POST[BLOG_POST_AUTHOR_FIELD_NAME],
				BLOG_POST_CONTENT_FIELD_NAME => $_POST[BLOG_POST_CONTENT_FIELD_NAME],
			];

			$result = $this->update($postData);

			//
			// TODO redirect to the blog post reader page instead.
			//
			header('Location: /index.php');

			return $result;
		}

		if(isset($_GET[ACTION_NAME_BLOG_POST_REMOVAL]))
		{
			if(!$this->checkDeleteRequest())
			{
				return false;
			}

			$postData = [
				BLOG_POST_ID_FIELD_NAME => $_POST[BLOG_POST_ID_FIELD_NAME],
			];

			$result = $this->delete($postData);

			header('Location: /index.php');

			return $result;
		}

		return false;
	}
}

$blogDriver = new BlogDriver();

$blogDriver->run();

?>
