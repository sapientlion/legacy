<?php

require_once(__DIR__ . '/../../config.php');
require_once(SITE_ROOT . '/core/interfaces/iblog_controller.php');
require_once(SITE_ROOT . '/core/models/post.php');

class BlogController implements IBlogController
{
	private PDO $dbh;
	private Post $post;
	
	/**
	 * Create a new blog post.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 * @throws PDOException — On error if PDO::ERRMODE_EXCEPTION option is true.
	 */
	private function doCreate() : bool
	{
		if(!$this->validate())
		{
			return false;
		}

		$stmt = $this->dbh->prepare("INSERT INTO post (title, author, content) VALUES (:title, :author, :content)");

		$stmt->bindParam(':title', $this->post->title);
		$stmt->bindParam(':author', $this->post->author);
		$stmt->bindParam(':content', $this->post->content);

		$result = $stmt->execute();

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
		if(!$this->validate())
		{
			return false;
		}

		//
		// TODO get rid of this and instead use an array of $_POST values.
		//
		$title = $this->post->title;
		$author = $this->post->author;
		$content = $this->post->content;

		$stmt = $this->dbh->prepare("UPDATE post SET title = :title, author = :author, content = :content WHERE id = :id");

		$stmt->bindParam(':title', $title);
		$stmt->bindParam(':author', $author);
		$stmt->bindParam(':content', $content);
		$stmt->bindParam(':id', $id);

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
		$stmt = $this->dbh->prepare("DELETE FROM post WHERE id = :id");

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
	public function __construct(?Post $post = NULL)
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
			$stmt = $this->dbh->prepare("SELECT * FROM post WHERE id = :id");

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
			$stmt = $this->dbh->prepare("SELECT * FROM post WHERE id BETWEEN :from AND :to");

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
	public function validate(?Post $post = NULL) : bool
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
	
	/**
	 * Run certain methods depending on given $_GET values.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 */
	public function run() : bool
	{
		$post = ['', '', '', ''];

		if(isset($_POST['id']) &&
		isset($_POST['title']) &&
		isset($_POST['author']) &&
		isset($_POST['content']))
		{
			$post = [$_POST['id'], $_POST['title'], $_POST['author'], $_POST['content']];
		}
		else
		{
			return false;
		}

		$blogController = new BlogController(
			new Post($post[1], $post[2], $post[3]));
	
		if(isset($_GET['create'])) 
		{
			$blogController->create();
			header('Location: ../../index.php');
		}
	
		if(isset($_GET['update'])) 
		{
			$blogController->update($post[0]);
			header('Location: ../../update.php');
		}
	
		if(isset($_GET['delete'])) 
		{
			$blogController->delete($post[0]);
			header('Location: ../../index.php');
		}
	
		return true;
	}
}

$blogController = new BlogController();
$blogController->run();

?>
