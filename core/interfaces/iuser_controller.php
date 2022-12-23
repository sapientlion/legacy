<?php

interface IUserController
{
	public function create() : bool;
	public function read() : bool;
	public function update() : bool;
	public function delete() : bool;
	public function signIn() : bool;
	public function signOut() : bool;
}

?>
