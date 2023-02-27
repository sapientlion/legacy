<?php

if(session_status() === PHP_SESSION_NONE) 
{
	session_start();
}

require_once(__DIR__ . '/../../config.php');
require_once(SITE_ROOT . '/core/controllers/system_controller.php');
require_once(SITE_ROOT . '/core/interfaces/iblog_controller.php');
require_once(SITE_ROOT . '/core/models/blog_post.php');
require_once(SITE_ROOT . '/core/settings/get.php');
require_once(SITE_ROOT . '/core/settings/input.php');
require_once(SITE_ROOT . '/core/settings/paths.php');
require_once(SITE_ROOT . '/core/settings/session.php');

class BlogController extends SystemController implements IBlogController
{
	private PDO $dbh;
	private BlogPost $post;
	
	/**
	 * Create a new blog post.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doCreate() : bool
	{
		//
		// This will always be triggered in the event where user is not logged in.
		//
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_CAN_CREATE_POSTS]) && empty($_SESSION[SESSION_VAR_NAME_USER_CAN_CREATE_POSTS]))
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('BlogController', 'doCreate', 'You must be logged-in in order to access this feature');
			}

			return false;
		}

		if(!$this->validate())
		{
			if(SYSTEM_DEBUGGING)
			{
				$this->report('BlogController', 'doCreate', 'Unable to validate given data');
			}

			return false;
		}

		//
		// INSERT INTO post (title, author, content) VALUES (:title, :author, :content)
		//
		$string = "INSERT INTO " . 
		DB_TABLE_BLOG_POST . " (" . 
		DB_TABLE_BLOG_POST_TITLE . ", " . 
		DB_TABLE_BLOG_POST_USER . ", " . 
		DB_TABLE_BLOG_POST_CONTENT . ") VALUES (:" . 
		DB_TABLE_BLOG_POST_TITLE . ", :" . 
		DB_TABLE_BLOG_POST_USER . ", :" . 
		DB_TABLE_BLOG_POST_CONTENT . ")";

		$stmt = $this->dbh->prepare($string);

		$stmt->bindParam(':' . DB_TABLE_BLOG_POST_TITLE , $this->post->title);
		$stmt->bindParam(':' . DB_TABLE_BLOG_POST_USER, $this->post->author);
		$stmt->bindParam(':' . DB_TABLE_BLOG_POST_CONTENT, $this->post->content);

		$result = $stmt->execute();

		return $result;
	}
	
	/**
	 * Fetch a blog post from DB.
	 *
	 * @param  int $id identification number of a blog post.
	 * @return array Filled with blog post data.
	 */
	private function doRead(int $id) : array
	{
		//
		// TODO i don't think that this should exist. Re-imagine this part or remove completely.
		//
		/*if(!isset($_SESSION[SESSION_VAR_NAME_USER_CAN_UPDATE_POSTS]) && empty($_SESSION[SESSION_VAR_NAME_USER_CAN_UPDATE_POSTS]))
		{
			return array();
		}*/

		//
		// SELECT * FROM post WHERE id = :id
		//
		$string = "SELECT * FROM " . 
		DB_TABLE_BLOG_POST . " WHERE " . 
		DB_TABLE_BLOG_POST_ID . " = :" . 
		DB_TABLE_BLOG_POST_ID;

		$stmt = $this->dbh->prepare($string);

		$stmt->bindParam(':' . DB_TABLE_BLOG_POST_ID, $id);
		$stmt->execute();
		$result = $stmt->fetch();

		//
		// Previous statement may cause a runtime exception (it will return FALSE on failure) because of given 
		// non-existing blog post ID. Circumvent that by returning an empty array.
		//
		if(!$result)
		{
			return array();
		}

		//
		// Sanitize output to prevent from potential bugs and XSS attacks.
		//
		foreach($result as $blogPostAttribute)
		{
			$blogPostAttribute = htmlspecialchars($blogPostAttribute);
		}

		return $result;
	}
	
	/**
	 * Fetch all blog posts from DB.
	 *
	 * @param  int $from ID to begin from.
	 * @param  int $to ID to stop at.
	 * @return array Of all known blog posts.
	 */
	private function doReadAll() : array
	{
		//
		// SELECT * FROM post WHERE id BETWEEN :from AND :to
		//
		$string = "SELECT * FROM " . 
		DB_TABLE_BLOG_POST;
		
		$stmt = $this->dbh->prepare($string);

		$stmt->execute();

		$result = $stmt->fetchAll();

		//
		// Also return an empty array in case of a failure (empty list for example).
		//
		if(!$result)
		{
			return array();
		}

		foreach($result as $blogPost)
		{
			foreach($blogPost as $blogPostAttribute)
			{
				$blogPostAttribute = htmlspecialchars($blogPostAttribute);
			}
		}
	
		return $result;
	}
	
	/**
	 * Update preceding blog post with new data.
	 *
	 * @param  int $id ID of a blog post.
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doUpdate(int $id) : bool
	{
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_CAN_UPDATE_POSTS]) && empty($_SESSION[SESSION_VAR_NAME_USER_CAN_UPDATE_POSTS]))
		{
			return false;
		}

		if(!$this->validate())
		{
			return false;
		}

		$title = $this->post->title;
		$author = $this->post->author;
		$content = $this->post->content;

		//
		// UPDATE post SET title = :title, author = :author, content = :content WHERE id = :id
		//
		$string = "UPDATE " . 
		DB_TABLE_BLOG_POST . " SET " . 
		DB_TABLE_BLOG_POST_TITLE . " = :" . 
		DB_TABLE_BLOG_POST_TITLE . ", " . 
		DB_TABLE_BLOG_POST_USER . " = :" . 
		DB_TABLE_BLOG_POST_USER . ", " . 
		DB_TABLE_BLOG_POST_CONTENT . " = :" . 
		DB_TABLE_BLOG_POST_CONTENT . " WHERE " . DB_TABLE_BLOG_POST_ID . " = :" . DB_TABLE_BLOG_POST_ID;

		$stmt = $this->dbh->prepare($string);

		$stmt->bindParam(':' . DB_TABLE_BLOG_POST_TITLE, $title);
		$stmt->bindParam(':' . DB_TABLE_BLOG_POST_USER, $author);
		$stmt->bindParam(':' . DB_TABLE_BLOG_POST_CONTENT, $content);
		$stmt->bindParam(':' . DB_TABLE_BLOG_POST_ID, $id);

		$result = $stmt->execute();

		return $result;
	}
	
	/**
	 * Remove preceding blog post from database.
	 *
	 * @param  int $id ID of a blog post.
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doDelete(int $id) : bool
	{
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_CAN_DELETE_POSTS]) && empty($_SESSION[SESSION_VAR_NAME_USER_CAN_DELETE_POSTS]))
		{
			return false;
		}

		//
		// DELETE FROM post WHERE id = :id
		//
		$string = "DELETE FROM " . 
		DB_TABLE_BLOG_POST . " WHERE " . 
		DB_TABLE_BLOG_POST_ID . " = :" . 
		DB_TABLE_BLOG_POST_ID;

		$stmt = $this->dbh->prepare($string);

		$stmt->bindParam(':' . DB_TABLE_BLOG_POST_ID, $id);

		$result = $stmt->execute();

		return $result;
	}
	
	/**
	 * Class constructor.
	 *
	 * @return void
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function __construct(?BlogPost $post = NULL)
	{
		if(is_null($post))
		{
			return;
		}

		$this->post = $post;

		try
		{
			$this->dbh = new PDO('mysql:host=' . DB_HOSTNAME . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);

			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';
		}
	}
	
	/**
	 * A method wrapper used for blog post creation in DB.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function create() : bool
	{
		try
		{
			$result = $this->doCreate();

			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';

			return false;
		}
	}
	
	/**
	 * Get a single blog post entry from DB.
	 *
	 * @param  int $id ID of preceding blog post.
	 * @return array|null if everything is ok, return a blog post represented as an array of attributes. Return null,
	 * if something's wrong with the database or if a wrong ID was supplied prior to method execution. 
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function read(int $id) : array
	{
		try
		{
			$result = $this->doRead($id);

			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';

			return null;
		}
	}
	
	/**
	 * Get all possible preceding blog posts within the given range.
	 *
	 * @param  int $from ID to begin from.
	 * @param  int $to ID to stop at.
	 * @return array|null array of blog posts or null, if nothing was found or if there's a problem with the DB.
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function readAll() : array
	{
		try
		{
			$result = $this->doReadAll();
	
			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';

			return null;
		}
	}
	
	/**
	 * A method wrapper used for blog post update in DB.
	 *
	 * @param  int $id ID of preceding blog post.
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function update(int $id) : bool
	{
		try
		{
			$result = $this->doUpdate($id);

			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';

			return false;
		}
	}
	
	/**
	 * A method wrapper used for blog post removal from DB.
	 *
	 * @param  int $id ID of preceding blog post.
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function delete(int $id) : bool
	{
		try
		{
			$result = $this->doDelete($id);
			
			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';

			return false;
		}
	}

	/**
	* Validate all given blog post data.
	*
	* @return bool TRUE on success or FALSE on failure.
	*/
	public function validate(?BlogPost $post = NULL) : bool
	{
		$blogPostTitle = '';
		$blogPostAuthor = '';
		$blogPostContent = '';

		//
		// Always prioritize the local instance of the object.
		//
		if(!is_null($post))
		{
			$blogPostTitle = $post->title;
			$blogPostAuthor = $post->author;
			$blogPostContent = $post->content;
		}
		else
		{
			$blogPostTitle = $this->post->title;
			$blogPostAuthor = $this->post->author;
			$blogPostContent = $this->post->content;
		}

		//
		// It's possible that these variables might end up being empty.
		//
		if(empty($blogPostTitle) || empty($blogPostAuthor) || empty($blogPostContent))
		{
			return false;
		}

		$blogPostTitleLen = strlen($blogPostTitle);

		if($blogPostTitleLen <= 0 || $blogPostTitleLen > DATA_BLOG_POST_TITLE_LENGTH)
		{
			return false;
		}

		$blogPostAuthorLen = strlen($blogPostAuthor);

		if($blogPostAuthorLen <= 0 || $blogPostAuthorLen > DATA_BLOG_POST_USER_LENGTH)
		{
			return false;
		}

		$blogPostContentLen = strlen($blogPostContent);

		if($blogPostContentLen <= 0 || $blogPostContentLen > DATA_BLOG_POST_CONTENT_LENGTH)
		{
			return false;
		}

		return true;
	}
	
	/**
	 * Get total number of blog posts that exist in DB.
	 *
	 * @return int total number of preceding blog posts or 0 if nothing was found in DB.
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function getRowNum() : int
	{
		try
		{
			$stmt = $this->dbh->prepare("SELECT * FROM post");
			$stmt->execute();
			$result = $stmt->fetchAll();
			$result = count($result);
	
			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';

			return 0;
		}
	}

	public function getCreationForm() : string
	{
		$form = '<form action="' . BLOG_CREATION_PATH . '" method="post">
		<label for="' . BLOG_POST_TITLE_FIELD_NAME . '">Title:</label><br>
		<input type="text" id="' . BLOG_POST_TITLE_FIELD_NAME . '" name="' . BLOG_POST_TITLE_FIELD_NAME . '"><br>
		
		<label for="' . BLOG_POST_CONTENT_FIELD_NAME . '">Content:</label><br>
		<input type="text" id="' . BLOG_POST_CONTENT_FIELD_NAME . '" name="' . BLOG_POST_CONTENT_FIELD_NAME . '"><br>
		
		<button type="submit" value="Post" id="submission-button">Post</button>
		</form>';

		if(isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && !empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return $form;
		}

		return '';
	}

	public function getViewForm(int $blogPostID) : string
	{
		$result = $this->read($blogPostID);

		$form = '<form action="' . BLOG_CREATION_PATH . '" method="post">
		<label for="' . BLOG_POST_TITLE_FIELD_NAME . '">Title:</label><br>
		<input type="text" id="' . BLOG_POST_TITLE_FIELD_NAME . '" name="' . 
		BLOG_POST_TITLE_FIELD_NAME . '" value="' . $result[DB_TABLE_BLOG_POST_TITLE] . '" readonly><br>
		
		<label for="' . BLOG_POST_AUTHOR_FIELD_NAME .  '">Author:</label><br>
		<input type="text" id="' . BLOG_POST_AUTHOR_FIELD_NAME . '" name="' . 
		BLOG_POST_AUTHOR_FIELD_NAME . '" value="' . $result[DB_TABLE_BLOG_POST_USER] . '" readonly><br>
		
		<label for="' . BLOG_POST_CONTENT_FIELD_NAME . '">Content:</label><br>
		<input type="text" id="' . BLOG_POST_CONTENT_FIELD_NAME . '" name="' . 
		BLOG_POST_CONTENT_FIELD_NAME . '" value="' . $result[DB_TABLE_BLOG_POST_CONTENT] . '" readonly>br>
		
		<button type="submit" value="Post" id="submission-button">Update</button>
		</form>';

		return $form;
	}

	public function getViewForms(int $from = 0) : array
	{
		$result = $this->readAll();
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
					$blogPost = '<form class="master blog-post" action="core/controllers/blog_controller.php" method="post">
					<input class="hidden" type="text" id="' . BLOG_POST_ID_FIELD_NAME . '-' . $post[DB_TABLE_BLOG_POST_ID] . '" name="' . BLOG_POST_ID_FIELD_NAME . '" value="' . $post[DB_TABLE_BLOG_POST_ID] . '" readonly><br>
					
					<input type="text" id="' . BLOG_POST_TITLE_FIELD_NAME . '" name="' . BLOG_POST_TITLE_FIELD_NAME . '" value="Title: ' . $post[DB_TABLE_BLOG_POST_TITLE] . '" readonly><br>
					
					<input type="text" id="' . BLOG_POST_AUTHOR_FIELD_NAME . '" name="' . BLOG_POST_AUTHOR_FIELD_NAME . '" value="Author: ' . $post[DB_TABLE_BLOG_POST_USER] . '" readonly><br>
					
					<input type="text" id="' . BLOG_POST_CONTENT_FIELD_NAME . '" name="' . BLOG_POST_CONTENT_FIELD_NAME . '" value="' . $post[DB_TABLE_BLOG_POST_CONTENT] . '" readonly><br>

					<div class="blog-post-controller">
						<button type="submit" name="action" value="read">Read</button>
					</div>
					
					</form>';
					
					print($blogPost);
				}
			}
			else
			{
				foreach ($result as $post) 
				{
					$blogPost = '<form class="master blog-post" action="core/controllers/blog_controller.php" method="post">
					<input class="hidden" type="text" id="' . BLOG_POST_ID_FIELD_NAME . '-' . $post[DB_TABLE_BLOG_POST_ID] . '" name="' . BLOG_POST_ID_FIELD_NAME . '" value="' . $post[DB_TABLE_BLOG_POST_ID] . '" readonly><br>
					
					<input type="text" id="' . BLOG_POST_TITLE_FIELD_NAME . '" name="' . BLOG_POST_TITLE_FIELD_NAME . '" value="Title: ' . $post[DB_TABLE_BLOG_POST_TITLE] . '" readonly><br>
					
					<input type="text" id="' . BLOG_POST_AUTHOR_FIELD_NAME . '" name="' . BLOG_POST_AUTHOR_FIELD_NAME . '" value="Author: ' . $post[DB_TABLE_BLOG_POST_USER] . '" readonly><br>
													
					<input type="text" id="' . BLOG_POST_CONTENT_FIELD_NAME . '" name="' . BLOG_POST_CONTENT_FIELD_NAME . '" value="' . $post[DB_TABLE_BLOG_POST_CONTENT] . '" readonly><br>
					
					<div class="blog-post-controller">
						<button type="submit" name="action" value="read">Read</button>
						<button type="submit" name="action" value="update">Update</button>
						<button type="submit" name="action" value="delete">Delete</button>
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

	public function getUpdateForm(int $blogPostID) : string
	{
		$result = $this->read($blogPostID);

		$form = '<form action="' . BLOG_CREATION_PATH . '" method="post">
		<label for="' . BLOG_POST_TITLE_FIELD_NAME . '">Title:</label><br>
		<input type="text" id="' . BLOG_POST_TITLE_FIELD_NAME . '" name="' . 
		BLOG_POST_TITLE_FIELD_NAME . '" value="' . $result[DB_TABLE_BLOG_POST_TITLE] . '"><br>
		
		<label for="' . BLOG_POST_CONTENT_FIELD_NAME . '">Content:</label><br>
		<input type="text" id="' . BLOG_POST_CONTENT_FIELD_NAME . '" name="' . 
		BLOG_POST_CONTENT_FIELD_NAME . '" value="' . $result[DB_TABLE_BLOG_POST_CONTENT] . '">br>
		
		<button type="submit" value="Post" id="submission-button">Update</button>
		</form>';

		if(isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && !empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return $form;
		}

		return '';
	}
}

?>
