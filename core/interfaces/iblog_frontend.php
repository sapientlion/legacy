<?php

interface IBlogFrontend
{
	public function getCreator() : string;
	public function getPost(int $blogPostID) : string;
	public function getPosts(int $from = 0) : array;
	public function getEditor(int $blogPostID) : string;
	public function getPageSelector(int $from = 0) : int;
}

?>
