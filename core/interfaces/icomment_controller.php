<?php

interface ICommentController
{
	public function create() : bool;
	public function read(int $id) : array;
	public function readAll(int $from, int $to) : array;
	public function update(int $id) : bool;
	public function delete(int $id) : bool;
}

?>