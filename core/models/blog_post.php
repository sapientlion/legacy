<?php

class BlogPost
{
	public int $id;
	public string $title;
	public string $author;
	public string $content;

	public function __construct(string $title, string $author, string $content, int $id = -1)
	{
		$this->id = $id;
		$this->title = $title;
		$this->author = $author;
		$this->content = $content;
	}
}

?>
