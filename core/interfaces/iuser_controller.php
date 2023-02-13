<?php

interface IUserController
{
	public function create() : bool;
	public function read() : bool;
	public function update(string $currentUsername) : bool;
	public function delete(string $currentUsername) : bool;
	public function tryToFindMatchingEntries(string $username = '', string $email = '') : int;
	public function validatePassword(string $password = '', string $confirmationPassword = '') : string;
	public function signIn() : bool;
	public function signOut() : bool;
	public function getHeader() : string;
	public function getSignupForm() : string;
	public function getSigninForm() : string;
	public function run() : bool;
}

?>
