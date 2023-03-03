<?php

interface ICommentFrontend
{
	public function getCreator() : string;
	public function getComments(int $from = 0) : array;
	public function getPageSelector(int $from = 0) : int;
}

?>
