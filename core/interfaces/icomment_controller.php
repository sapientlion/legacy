<?php

interface ICommentController
{
	public function create() : bool;
	public function read() : array;
	public function readAll() : array;
	public function update() : bool;
	public function delete() : bool;
}

?>
