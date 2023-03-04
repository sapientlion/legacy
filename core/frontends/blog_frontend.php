<?php

if(session_status() === PHP_SESSION_NONE)
{
	session_start();
}

require_once(__DIR__ . '/../../config.php');
require_once(SITE_ROOT . '/core/controllers/blog_controller.php');
require_once(SITE_ROOT . '/core/interfaces/iblog_frontend.php');
require_once(SITE_ROOT . '/core/settings/get.php');
require_once(SITE_ROOT . '/core/settings/input.php');
require_once(SITE_ROOT . '/core/settings/paths.php');
require_once(SITE_ROOT . '/core/settings/session.php');

class BlogFrontend extends BlogController
{	
	/**
	 * Get a blog post creator.
	 *
	 * @return string a blog post creator on success and an empty string on failure.
	 */
	public function getCreator() : string
	{
		$form = '<form action="' . BLOG_CREATION_PATH . '" method="post">
		<label for="' . BLOG_POST_TITLE_FIELD_NAME . '">Title:</label><br>
		<input type="text" id="' . BLOG_POST_TITLE_FIELD_NAME . '" name="' . BLOG_POST_TITLE_FIELD_NAME . '"><br>
		
		<label for="' . BLOG_POST_CONTENT_FIELD_NAME . '">Content:</label><br>
		<textarea id="' . BLOG_POST_CONTENT_FIELD_NAME . '" name="' . BLOG_POST_CONTENT_FIELD_NAME . '" rows="25" cols="150"></textarea><br>
		
		<div class="blog-post-controller">
			<button type="submit" formaction="' . BLOG_CREATION_PATH . '" name="' . BLOG_POST_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_BLOG_POST_CREATION . '">Post</button>
		</div>

		</form>';

		if(isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && !empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return $form;
		}

		return '';
	}
	
	/**
	 * Get a single blog post from DB.
	 *
	 * @param  int $blogPostID ID number of a desired blog post.
	 * @return string typical blog post filled with data.
	 */
	public function getPost() : string
	{
		$result = $this->read($this->post->id);

		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			$form = '<form class="blog-post" method="post">
			<div class="blog-post-top">
				<label for="' . BLOG_POST_TITLE_FIELD_NAME . '">Title:</label><br>
				<input type="text" id="' . BLOG_POST_TITLE_FIELD_NAME . '" name="' . 
				BLOG_POST_TITLE_FIELD_NAME . '" value="' . $result[DB_TABLE_BLOG_POST_TITLE] . '" readonly><br>
			
				<label for="' . BLOG_POST_AUTHOR_FIELD_NAME .  '">Author:</label><br>
				<input type="text" id="' . BLOG_POST_AUTHOR_FIELD_NAME . '" name="' . 
				BLOG_POST_AUTHOR_FIELD_NAME . '" value="' . $result[DB_TABLE_BLOG_POST_USER] . '" readonly><br>
			</div>
	
			<div class="blog-post-middle">
				<textarea id="' . BLOG_POST_CONTENT_FIELD_NAME . '" name="' . 
				BLOG_POST_CONTENT_FIELD_NAME . '" rows="25" cols="190" readonly>' . $result[DB_TABLE_BLOG_POST_CONTENT] . '</textarea><br>
			</div>
	
			</form>';

			return $form;
		}

		$form = '<form class="blog-post" method="post">
		<div class="blog-post-top">
			<label for="' . BLOG_POST_TITLE_FIELD_NAME . '">Title:</label><br>
			<input type="text" id="' . BLOG_POST_TITLE_FIELD_NAME . '" name="' . 
			BLOG_POST_TITLE_FIELD_NAME . '" value="' . $result[DB_TABLE_BLOG_POST_TITLE] . '" readonly><br>
		
			<label for="' . BLOG_POST_AUTHOR_FIELD_NAME .  '">Author:</label><br>
			<input type="text" id="' . BLOG_POST_AUTHOR_FIELD_NAME . '" name="' . 
			BLOG_POST_AUTHOR_FIELD_NAME . '" value="' . $result[DB_TABLE_BLOG_POST_USER] . '" readonly><br>
		</div>

		<div class="blog-post-middle">
			<textarea id="' . BLOG_POST_CONTENT_FIELD_NAME . '" name="' . 
			BLOG_POST_CONTENT_FIELD_NAME . '" rows="25" cols="190" readonly>' . $result[DB_TABLE_BLOG_POST_CONTENT] . '</textarea><br>
		</div>
		
		<div class="blog-post-bottom">
			<button type="submit" formaction="' . BLOG_UPDATE_PAGE_PATH . '" name="' . BLOG_POST_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_BLOG_POST_UPDATE . '">Edit</button>
			<button type="submit" formaction="' . BLOG_REMOVAL_PATH . '" name="' . BLOG_POST_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_BLOG_POST_REMOVAL . '">Delete</button>
		</div>

		</form>';

		return $form;
	}
	
	/**
	 * Get multiple posts from DB.
	 *
	 * @param  int $from a starting point.
	 * @return array list of all blog posts located in DB.
	 */
	public function getPosts(int $from = 0, string $fFlag = '', string $keyword = '') : array
	{
		$result = array();

		if($fFlag > 0)
		{
			$result = $this->readAll($fFlag, $keyword);
		}
		else
		{
			$result = $this->readAll();
		}
		
		$totalPosts = count($result);

		if($totalPosts > 5)
		{
			$result = array_slice($result, $from, 5);
		}

		if($totalPosts > 0)
		{
			if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
			{
				foreach ($result as $post) 
				{
					$blogPost = '<form class="master blog-post" action="" method="post">
					<input class="hidden" type="text" id="' . BLOG_POST_ID_FIELD_NAME . '-' . $post[DB_TABLE_BLOG_POST_ID] . '" name="' . BLOG_POST_ID_FIELD_NAME . '" value="' . $post[DB_TABLE_BLOG_POST_ID] . '" readonly><br>
					
					<input type="text" id="' . BLOG_POST_TITLE_FIELD_NAME . '" name="' . BLOG_POST_TITLE_FIELD_NAME . '" value="Title: ' . $post[DB_TABLE_BLOG_POST_TITLE] . '" readonly><br>
					
					<input type="text" id="' . BLOG_POST_AUTHOR_FIELD_NAME . '" name="' . BLOG_POST_AUTHOR_FIELD_NAME . '" value="Author: ' . $post[DB_TABLE_BLOG_POST_USER] . '" readonly><br>
					
					<textarea id="' . BLOG_POST_CONTENT_FIELD_NAME . '" name="' . BLOG_POST_CONTENT_FIELD_NAME . '" rows="5" cols="150" readonly>' . $post[DB_TABLE_BLOG_POST_CONTENT] . '</textarea><br>

					<div class="blog-post-controller">
						<button type="submit" formaction="' . BLOG_VIEW_PAGE_PATH . '?post=' . $post[DB_TABLE_BLOG_POST_ID] . '" name="' . BLOG_POST_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_BLOG_POST_VIEW . '">View</button>
					</div>
					
					</form>';
					
					print($blogPost);
				}
			}
			else
			{
				foreach ($result as $post) 
				{
					$blogPost = '<form class="blog-post" method="post">
					<input class="hidden" type="text" id="' . BLOG_POST_ID_FIELD_NAME . '-' . $post[DB_TABLE_BLOG_POST_ID] . '" name="' . BLOG_POST_ID_FIELD_NAME . '" value="' . $post[DB_TABLE_BLOG_POST_ID] . '" readonly><br>
					
					<input type="text" id="' . BLOG_POST_TITLE_FIELD_NAME . '" name="' . BLOG_POST_TITLE_FIELD_NAME . '" value="' . $post[DB_TABLE_BLOG_POST_TITLE] . '" readonly><br>
					
					<input type="text" id="' . BLOG_POST_AUTHOR_FIELD_NAME . '" name="' . BLOG_POST_AUTHOR_FIELD_NAME . '" value="' . $post[DB_TABLE_BLOG_POST_USER] . '" readonly><br>
													
					<textarea id="' . BLOG_POST_CONTENT_FIELD_NAME . '" name="' . BLOG_POST_CONTENT_FIELD_NAME . '" rows="5" cols="150" readonly>' . $post[DB_TABLE_BLOG_POST_CONTENT] . '</textarea><br>
					
					<div class="blog-post-controller">
						<button type="submit" formaction="' . BLOG_VIEW_PAGE_PATH . '?post=' . $post[DB_TABLE_BLOG_POST_ID] . '" name="' . BLOG_POST_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_BLOG_POST_VIEW . '">View</button>

						<button type="submit" formaction="' . BLOG_UPDATE_PAGE_PATH . '" name="' . BLOG_POST_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_BLOG_POST_UPDATE . '">Edit</button>

						<button type="submit" formaction="' . BLOG_REMOVAL_PATH . '" name="' . BLOG_POST_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_BLOG_POST_REMOVAL . '">Delete</button>
					</div>
					
					</form>';
													
					print($blogPost);
				}
			}
		}
		else
		{
			$blogPost = '<form class="master blog-post"></form>';

			print($blogPost);
		}

		return $result;
	}
	
	/**
	 * Get a blog post editor.
	 *
	 * @param  int $blogPostID ID number of a desired blog post.
	 * @return string an editor with some data which can be freely edited by user on success and an empty string on failure.
	 */
	public function getEditor() : string
	{
		$result = $this->read($this->post->id);

		$form = '<form action="' . BLOG_CREATION_PATH . '" method="post">

		<input class="hidden" type="text" id="' . BLOG_POST_ID_FIELD_NAME . '-' . $result[DB_TABLE_BLOG_POST_ID] . '" name="' . BLOG_POST_ID_FIELD_NAME . '" value="' . $result[DB_TABLE_BLOG_POST_ID] . '" readonly><br>
		<input class="hidden" type="text" id="' . BLOG_POST_AUTHOR_FIELD_NAME . '-' . $result[DB_TABLE_BLOG_POST_USER] . '" name="' . BLOG_POST_AUTHOR_FIELD_NAME . '" value="' . $result[DB_TABLE_BLOG_POST_USER] . '" readonly><br>

		<label for="' . BLOG_POST_TITLE_FIELD_NAME . '">Title:</label><br>
		<input type="text" id="' . BLOG_POST_TITLE_FIELD_NAME . '" name="' . 
		BLOG_POST_TITLE_FIELD_NAME . '" value="' . $result[DB_TABLE_BLOG_POST_TITLE] . '"><br>
		
		<label for="' . BLOG_POST_CONTENT_FIELD_NAME . '">Content:</label><br>
		<textarea id="' . BLOG_POST_CONTENT_FIELD_NAME . '" name="' . 
		BLOG_POST_CONTENT_FIELD_NAME . '" rows="25" cols="150">' . $result[DB_TABLE_BLOG_POST_CONTENT] . '</textarea><br>
		
		<div class="blog-post-controller">
			<button type="submit" formaction="' . BLOG_UPDATE_PATH . '" name="' . BLOG_POST_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_BLOG_POST_UPDATE . '">Update</button>
		</div>

		</form>';

		if(isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && !empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return $form;
		}

		return '';
	}
	
	/**
	 * 
	 *
	 * @param  int $from a starting point.
	 * @return int total number of pages reserved for blog post storage.
	 */
	public function getPageSelector(int $from = 0) : int
	{
		//
		// TODO optimize this part, if possible.
		//
		$result = $this->readAll();
		$totalPosts = count($result);

		if($totalPosts <= 5)
		{
			return 0;
		}

		//
		// Get total number of pages required for storing the blog posts.
		//
		$totalPages = (int)($totalPosts / 5);

		//
		// Add another page when remainder is greater than zero. Example: $totalPosts = 6, $postsPerPage = 5.
		// $totalPosts / $postsPerPage = 1.2 = $totalPages. $totalPages is equal to 2 pages.
		//
		if($totalPosts % 5 > 0)
		{
			$totalPages++;
		}

		print('<ol class="master" id="page-selector">');

		//
		// Page selector itself.
		//
		for($page = 1; $page <= $totalPages; $page++)
		{
			print('<a href="index.php?from=' . $from + (($page - 1) * 5) . '">' .  $page . '</a>');
		}

		print('</ol>');

		return $totalPages;
	}

	public function getSearchBar() : string
	{
		$form = '<form class="master search-bar" method="get">
		<input type="text" id="' . 
		BLOG_POST_SEARCH_INPUT_FIELD_NAME . '" name="' . 
		BLOG_POST_SEARCH_INPUT_FIELD_NAME . '"><br>

		<select id="' .
			BLOG_POST_SEARCH_FILTER_FIELD_NAME . '" name="' . 
			BLOG_POST_SEARCH_FILTER_FIELD_NAME . '">
			<option value="' . BLOG_POST_SEARCH_TITLE_FIELD_NAME . '">by ' . BLOG_POST_SEARCH_TITLE_FIELD_NAME . '</option>
			<option value="' . BLOG_POST_SEARCH_AUTHOR_FIELD_NAME . '">by ' . BLOG_POST_SEARCH_AUTHOR_FIELD_NAME . '</option>
		</select>

			<button type="submit" formaction="' . 
				BLOG_ACTION_PATH . '" name="' .
				BLOG_POST_SUBMIT_BUTTON_NAME . '" value="' . 
				ACTION_NAME_BLOG_POST_SEARCH . '">Search
			</button>
		</form>';

		return $form;
	}
}

?>
