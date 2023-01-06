<?php

class User
{
	private string $username;
	private string $email;
	private string $password;
	private string $confirmationPassword;

	public function __construct(string $username, string $email, string $password = '', string $confirmationPassword = '')
	{
		$this->username = htmlspecialchars($username);
		$this->email = htmlspecialchars($email);
		$this->password = htmlspecialchars($password);
		$this->confirmationPassword = htmlspecialchars($confirmationPassword);
	}

	public function getUsername() : string
	{
		return $this->username;
	}

	public function getEmail() : string
	{
		return $this->email;
	}

	public function getPassword() : string
	{
		return $this->password;
	}

	public function getConfirmationPassword() : string
	{
		return $this->confirmationPassword;
	}
}

?>
