<?php

require_once(__DIR__ . '/../../config.php');
require_once(SITE_ROOT . '/core/interfaces/icomment_controller.php');
require_once(SITE_ROOT . '/core/models/comment.php');

class CommentController implements ICommentController
{
	private PDO $dbh;
	private Comment $comment;
	
	/**
	 * Create a new blog comment.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doCreate() : bool
	{
		//
		// INSERT INTO comment (post, author, content) VALUES (:post, :author, :content)
		//
		$query = "INSERT INTO " . DB_TABLE_COMMENT . " (" . 
		DB_TABLE_COMMENT_POST_ID . ", " . 
		DB_TABLE_COMMENT_AUTHOR . ", " . 
		DB_TABLE_COMMENT_CONTENT . ") VALUES (:" . 
		DB_TABLE_COMMENT_POST_ID . ", :" . 
		DB_TABLE_COMMENT_AUTHOR . ", :" . 
		DB_TABLE_COMMENT_CONTENT . ")";

		$stmt = $this->dbh->prepare($query);

		$stmt->bindParam(':' . DB_TABLE_COMMENT_POST_ID, $this->comment->postID);
		$stmt->bindParam(':' . DB_TABLE_COMMENT_AUTHOR, $this->comment->author);
		$stmt->bindParam(':' . DB_TABLE_COMMENT_CONTENT, $this->comment->content);

		$result = $stmt->execute();

		return $result;
	}

	private function doReadAll() : array
	{
		//
		// SELECT * FROM post WHERE id BETWEEN :from AND :to
		//
		$string = "SELECT * FROM " . 
		DB_TABLE_COMMENT . " WHERE " . DB_TABLE_COMMENT_POST_ID . " = :" . DB_TABLE_COMMENT_POST_ID;
		
		$stmt = $this->dbh->prepare($string);

		$stmt->bindParam(':' . DB_TABLE_COMMENT_POST_ID, $this->comment->postID);

		$stmt->execute();

		$result = $stmt->fetchAll();

		//
		// Also return an empty array in case of a failure (empty list for example).
		//
		if(!$result)
		{
			return array();
		}

		foreach($result as $comment)
		{
			foreach($comment as $commentAttribute)
			{
				$commentAttribute = htmlspecialchars($commentAttribute);
			}
		}
	
		return $result;
	}
	
	/**
	 * Update preceding blog comment with new data.
	 *
	 * @param  int $id ID of a blog comment.
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doUpdate(int $id) : bool
	{
		$post = $this->comment->postID;
		$author = $this->comment->author;
		$content = $this->comment->content;

		//
		// UPDATE comment SET post = :post, author = :author, content = :content WHERE id = :id
		//
		$query = "UPDATE " . 
		DB_TABLE_COMMENT . " SET " . 
		DB_TABLE_COMMENT_POST_ID . " = :" . 
		DB_TABLE_COMMENT_POST_ID . ", " . 
		DB_TABLE_COMMENT_AUTHOR . " = :" . 
		DB_TABLE_COMMENT_AUTHOR . ", " . 
		DB_TABLE_COMMENT_CONTENT . " = :" . 
		DB_TABLE_COMMENT_CONTENT . " WHERE " . 
		DB_TABLE_COMMENT_ID . " = :" . 
		DB_TABLE_COMMENT_ID;

		$stmt = $this->dbh->prepare($query);

		$stmt->bindParam(':' . DB_TABLE_COMMENT_POST_ID, $post);
		$stmt->bindParam(':' . DB_TABLE_COMMENT_AUTHOR, $author);
		$stmt->bindParam(':' . DB_TABLE_COMMENT_CONTENT, $content);
		$stmt->bindParam(':' . DB_TABLE_COMMENT_ID, $id);

		$result = $stmt->execute();

		return $result;
	}
	
	/**
	 * Remove preceding blog comment from database.
	 *
	 * @param  int $id ID of a blog comment.
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doDelete(int $id) : bool
	{
		//
		// DELETE FROM comment WHERE id = :id
		//
		$query = "DELETE FROM " . 
		DB_TABLE_COMMENT . " WHERE " . 
		DB_TABLE_COMMENT_ID . " = :" . DB_TABLE_COMMENT_ID;

		$stmt = $this->dbh->prepare($query);

		$stmt->bindParam(':' . DB_TABLE_COMMENT_ID, $id);

		$result = $stmt->execute();

		return $result;
	}
	
	/**
	 * Class constructor.
	 *
	 * @return void
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function __construct(?Comment $comment = NULL)
	{
		if(is_null($comment))
		{
			return;
		}

		$this->comment = $comment;

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
	 * A method wrapper used for blog comment creation in DB.
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
	 * Get a single blog comment entry from DB.
	 *
	 * @param  int $id ID of preceding blog comment.
	 * @return array|null if everything is ok, return a blog comment represented as an array of attributes. Return null,
	 * if something's wrong with the database or if a wrong ID was supplied prior to method execution. 
	 * @throws PDOException On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	public function read(int $id) : array
	{
		try
		{
			//
			// SELECT * FROM comment WHERE id = :id
			//
			$query = "SELECT * FROM " . 
			DB_TABLE_COMMENT . " WHERE " . 
			DB_TABLE_COMMENT_ID . " = :" . DB_TABLE_COMMENT_ID;

			$stmt = $this->dbh->prepare($query);

			$stmt->bindParam(':' . DB_TABLE_COMMENT_ID, $id);
			$stmt->execute();
			$result = $stmt->fetch();
	
			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';

			return null;
		}
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
			$stmt = $this->dbh->prepare("SELECT * FROM comment");
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

			return false;
		}
	}
	
	/**
	 * A method wrapper used for blog comment update in DB.
	 *
	 * @param  int $id ID of preceding blog comment.
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
	 * A method wrapper used for blog comment removal from DB.
	 *
	 * @param  int $id ID of preceding blog comment.
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

	public function getCreationForm(int $postID) : string
	{
		$form = '<form method="post">
		<input class="hidden" type="text" id="' . COMMENT_POST_ID_FIELD_NAME . '-' . $postID . '" name="' . COMMENT_POST_ID_FIELD_NAME . '" value="' . $postID . '" readonly><br>
		<input class="hidden" type="text" id="' . COMMENT_AUTHOR_FIELD_NAME . '" name="' . COMMENT_AUTHOR_FIELD_NAME . '" value="' . $_SESSION[SESSION_VAR_NAME_USER_NAME] . '" readonly><br>

		<label for="' . COMMENT_CONTENT_FIELD_NAME . '">Content:</label><br>
		<textarea id="' . COMMENT_CONTENT_FIELD_NAME . '" name="' . COMMENT_CONTENT_FIELD_NAME . '" rows="25" cols="150"></textarea><br>
		
		<div class="blog-post-controller">
			<button type="submit" formaction="' . COMMENT_ACTION_PATH . '" name="' . COMMENT_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_COMMENT_CREATION . '">Post</button>
		</div>

		</form>';

		if(isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && !empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return $form;
		}

		return '';
	}

	public function getViewForms(int $from = 0) : array
	{
		$result = $this->readAll();
		$totalComments = count($result);

		if($totalComments > 5)
		{
			$result = array_slice($result, $from, 5);
		}

		if($totalComments > 0)
		{
			if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
			{
				foreach ($result as $post) 
				{
					$comment = '<form class="master blog-post" action="" method="post">
					<input class="hidden" type="text" id="' . COMMENT_ID_FIELD_NAME . '-' . $post[DB_TABLE_COMMENT_ID] . '" name="' . COMMENT_ID_FIELD_NAME . '" value="' . $post[DB_TABLE_COMMENT_ID] . '" readonly><br>
					<input class="hidden" type="text" id="' . COMMENT_POST_ID_FIELD_NAME . '-' . $post[DB_TABLE_COMMENT_POST_ID] . '" name="' . COMMENT_POST_ID_FIELD_NAME . '" value="' . $post[DB_TABLE_COMMENT_POST_ID] . '" readonly><br>
					
					<input type="text" id="' . COMMENT_AUTHOR_FIELD_NAME . '" name="' . COMMENT_AUTHOR_FIELD_NAME . '" value="Title: ' . $post[DB_TABLE_COMMENT_AUTHOR] . '" readonly><br>
					
					<textarea id="' . COMMENT_CONTENT_FIELD_NAME . '" name="' . COMMENT_CONTENT_FIELD_NAME . '" rows="5" cols="150" readonly>' . $post[DB_TABLE_COMMENT_CONTENT] . '</textarea><br>
					
					</form>';
					
					print($comment);
				}
			}
			else
			{
				foreach ($result as $post) 
				{
					$comment = '<form class="master blog-post" method="post">
					<input class="hidden" type="text" id="' . COMMENT_ID_FIELD_NAME . '-' . $post[DB_TABLE_COMMENT_ID] . '" name="' . COMMENT_ID_FIELD_NAME . '" value="' . $post[DB_TABLE_COMMENT_ID] . '" readonly><br>
					<input class="hidden" type="text" id="' . COMMENT_POST_ID_FIELD_NAME . '-' . $post[DB_TABLE_COMMENT_POST_ID] . '" name="' . COMMENT_POST_ID_FIELD_NAME . '" value="' . $post[DB_TABLE_COMMENT_POST_ID] . '" readonly><br>
					
					<input type="text" id="' . COMMENT_AUTHOR_FIELD_NAME . '" name="' . COMMENT_AUTHOR_FIELD_NAME . '" value="' . $post[DB_TABLE_COMMENT_AUTHOR] . '" readonly><br>
													
					<textarea id="' . COMMENT_CONTENT_FIELD_NAME . '" name="' . COMMENT_CONTENT_FIELD_NAME . '" rows="5" cols="150" readonly>' . $post[DB_TABLE_COMMENT_CONTENT] . '</textarea><br>
					
					<div class="blog-post-controller">
						<button type="submit" formaction="' . COMMENT_ACTION_PATH . '" name="' . COMMENT_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_COMMENT_UPDATE . '">Update</button>

						<button type="submit" formaction="' . COMMENT_ACTION_PATH . '" name="' . COMMENT_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_COMMENT_REMOVAL . '">Delete</button>
					</div>
					
					</form>';
													
					print($comment);
				}
			}
		}
		else
		{
			$comment = '<form class="master blog-post"></form>';

			print($comment);
		}

		return $result;
	}

	public function getPageSelector(int $from = 0) : int
	{
		//
		// TODO optimize this part, if possible.
		//
		$result = $this->readAll();
		$totalComments = count($result);

		if($totalComments <= 5)
		{
			return 0;
		}

		//
		// Get total number of pages required for storing the blog posts.
		//
		$totalPages = (int)($totalComments / 5);

		//
		// Add another page when remainder is greater than zero. Example: $totalComments = 6, $postsPerPage = 5.
		// $totalComments / $postsPerPage = 1.2 = $totalPages. $totalPages is equal to 2 pages.
		//
		if($totalComments % 5 > 0)
		{
			$totalPages++;
		}

		print('<ol class="master" id="page-selector">');

		//
		// Page selector itself.
		//
		for($page = 1; $page <= $totalPages; $page++)
		{
			print('<a href="index.php?post=' . $result[DB_TABLE_COMMENT_POST_ID] . '&from=' . $from + (($page - 1) * 5) . '">' .  $page . '</a>');
		}

		print('</ol>');

		return $totalPages;
	}
}

?>
