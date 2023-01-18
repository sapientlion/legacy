<?php

class Comment
{
	public int $id;
	public string $postID;
	public string $author;
	public string $content;

	public function __construct(int $postID, string $author, string $content, int $id = -1)
	{
		$this->id = $id;
		$this->postID = $postID;
		$this->author = $author;
		$this->content = $content;
	}
}

?>
