<?php

class User
{
	private string $username;
	private string $email;
	private string $pass;

	public function __construct(string $username, string $email, string $pass)
	{
		$this->username = htmlspecialchars($username);
		$this->email = htmlspecialchars($email);
		$this->pass = password_hash(htmlspecialchars($pass), PASSWORD_DEFAULT);
	}

	public function getUsername() : string
	{
		return $this->username;
	}

	public function getEmail() : string
	{
		return $this->email;
	}

	public function getPass() : string
	{
		return $this->pass;
	}

	public function setUsername(string $username) : void
	{
		$this->username = htmlspecialchars($username);
	}

	public function setEmail(string $email) : void
	{
		$this->email = htmlspecialchars($email);
	}

	public function setPass(string $pass) : void
	{
		$this->pass = password_hash(htmlspecialchars($pass), PASSWORD_DEFAULT);
	}
}

?>
