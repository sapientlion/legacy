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
		$stmt = $this->dbh->prepare("INSERT INTO comment (post, author, content) VALUES (:post, :author, :content)");

		$stmt->bindParam(':post', $this->comment->postID);
		$stmt->bindParam(':author', $this->comment->author);
		$stmt->bindParam(':content', $this->comment->content);

		$result = $stmt->execute();

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
		$title = $this->comment->postID;
		$author = $this->comment->author;
		$content = $this->comment->content;

		$stmt = $this->dbh->prepare("UPDATE comment SET post = :post, author = :author, content = :content WHERE id = :id");

		$stmt->bindParam(':post', $title);
		$stmt->bindParam(':author', $author);
		$stmt->bindParam(':content', $content);
		$stmt->bindParam(':id', $id);

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
		$stmt = $this->dbh->prepare("DELETE FROM comment WHERE id = :id");

		$stmt->bindParam(':id', $id);

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
			$stmt = $this->dbh->prepare("SELECT * FROM comment WHERE id = :id");

			$stmt->bindParam(':id', $id);
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
	public function readAll(int $from, int $to) : array
	{
		try
		{
			//
			// The following statement is incompatible with some of the SQL-based languages.
			//
			$stmt = $this->dbh->prepare("SELECT * FROM comment WHERE id BETWEEN :from AND :to");

			$stmt->bindParam(':from', $from);
			$stmt->bindParam(':to', $to);
			$stmt->execute();
			$result = $stmt->fetchAll();
	
			return $result;
		}
		catch(PDOException $e)
		{
			print 'Error!: ' . $e->getMessage() . '<br/>';

			return null;
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
}

?>
