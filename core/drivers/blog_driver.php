<?php

if(session_status() === PHP_SESSION_NONE) 
{
	session_start();
}

require_once(__DIR__ . '../../../config.php');
require_once(SITE_ROOT . '/core/controllers/blog_controller.php');
require_once(SITE_ROOT . '/core/controllers/system_controller.php');
require_once(SITE_ROOT . '/core/interfaces/iblog_driver.php');
require_once(SITE_ROOT . '/core/settings/get.php');
require_once(SITE_ROOT . '/core/settings/input.php');
require_once(SITE_ROOT . '/core/settings/paths.php');
require_once(SITE_ROOT . '/core/settings/session.php');

class BlogDriver extends SystemController implements IBlogDriver
{
	/**
	 * Check prerequisites for blog post creation.
	 *
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function checkCreateRequest() : bool
	{
		if(!isset($_POST[BLOG_POST_TITLE_FIELD_NAME]) && empty($_POST[BLOG_POST_TITLE_FIELD_NAME]))
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('BlogDriver', 'checkCreateRequest', 'Title name is not provided');
			}

			return false;
		}

		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('BlogDriver', 'checkCreateRequest', 'User name is not provided');
			}

			return false;
		}

		if(!isset($_POST[BLOG_POST_CONTENT_FIELD_NAME]) && empty($_POST[BLOG_POST_CONTENT_FIELD_NAME]))
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('BlogDriver', 'checkCreateRequest', 'Content is not provided');
			}

			return false;
		}

		return true;
	}

	private function checkReadRequest() : bool
	{
		if(!isset($_POST[BLOG_POST_ID_FIELD_NAME]) && empty($_POST[BLOG_POST_ID_FIELD_NAME]))
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
	 * Check prerequisites for blog post removal.
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
	 * Create a new blog post.
	 *
	 * @param  array $postData list of blog post information to insert into database.
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

	private function read(array $postData) : array
	{
		$blogPostController = new BlogController(
			new BlogPost(
			'',
			'',
			'')
		);

		$result = $blogPostController->read(
			$postData[BLOG_POST_ID_FIELD_NAME]
		);

		return $result;
	}

	private function readAll(array $searchData) : array
	{
		$blogPostController = new BlogController(
			new BlogPost(
			'',
			'',
			'')
		);

		$result = $blogPostController->readAll(
			$searchData[BLOG_POST_SEARCH_FILTER_FIELD_NAME],
			$searchData[BLOG_POST_SEARCH_INPUT_FIELD_NAME]
		);

		return $result;
	}
	
	/**
	 * Update preceding blog post.
	 *
	 * @param  array $postData list of blog post data to insert into database.
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
	 * Delete a blog post from database.
	 *
	 * @param  array $postData list of blog post data to insert into database.
	 * @return bool TRUE on success and FALSE on failure.
	 */
	private function delete(array $postData) : bool
	{
		$blogPostController = new BlogController(new BlogPost(
			'',
			'',
			'')
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
		if(isset($_POST[BLOG_POST_SUBMIT_BUTTON_NAME]) && $_POST[BLOG_POST_SUBMIT_BUTTON_NAME] === ACTION_NAME_BLOG_POST_CREATION)
		{
			if(!$this->checkCreateRequest())
			{
				return false;
			}

			$postData = [
				BLOG_POST_TITLE_FIELD_NAME => $_POST[BLOG_POST_TITLE_FIELD_NAME],
				BLOG_POST_AUTHOR_FIELD_NAME => $_SESSION[SESSION_VAR_NAME_USER_NAME],
				BLOG_POST_CONTENT_FIELD_NAME => $_POST[BLOG_POST_CONTENT_FIELD_NAME],
			];

			$result = $this->create($postData);

			header('Location: /index.php');

			return $result;
		}

		if(isset($_POST[BLOG_POST_SUBMIT_BUTTON_NAME]) && $_POST[BLOG_POST_SUBMIT_BUTTON_NAME] === ACTION_NAME_BLOG_POST_VIEW)
		{
			if(!$this->checkReadRequest())
			{
				return false;
			}

			$postData = [
				BLOG_POST_ID_FIELD_NAME => $_POST[BLOG_POST_ID_FIELD_NAME]
			];

			$result = $this->read($postData);

			header(
				'Location: ' . BLOG_VIEW_PAGE_PATH . '?post=' . $result[DB_TABLE_BLOG_POST_ID]
			);

			return $result;
		}

		if(isset($_GET[BLOG_POST_SEARCH_INPUT_FIELD_NAME]) && !empty($_GET[BLOG_POST_SEARCH_INPUT_FIELD_NAME]))
		{
			$searchData = [
				BLOG_POST_SEARCH_INPUT_FIELD_NAME => $_GET[BLOG_POST_SEARCH_INPUT_FIELD_NAME],
				BLOG_POST_SEARCH_FILTER_FIELD_NAME => $_GET[BLOG_POST_SEARCH_FILTER_FIELD_NAME]
			];
			
			$result = $this->readAll($searchData);

			header(
				'Location: /index.php?' . BLOG_POST_SEARCH_INPUT_FIELD_NAME . '=' . $_GET[BLOG_POST_SEARCH_INPUT_FIELD_NAME] . 
				'&' . BLOG_POST_SEARCH_FILTER_FIELD_NAME . '=' . $_GET[BLOG_POST_SEARCH_FILTER_FIELD_NAME]
			);

			return true;
		}

		if(isset($_POST[BLOG_POST_SUBMIT_BUTTON_NAME]) && $_POST[BLOG_POST_SUBMIT_BUTTON_NAME] === ACTION_NAME_BLOG_POST_UPDATE)
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

			header(
				'Location: ' . BLOG_VIEW_PAGE_PATH . '?post=' . $result[DB_TABLE_BLOG_POST_ID]
			);

			return $result;
		}

		if(isset($_POST[BLOG_POST_SUBMIT_BUTTON_NAME]) && $_POST[BLOG_POST_SUBMIT_BUTTON_NAME] === ACTION_NAME_BLOG_POST_REMOVAL)
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
