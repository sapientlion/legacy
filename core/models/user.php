<?php

class User
{
	private string $username;
	private string $email;
	private string $password;
	private string $confirmationPassword;

	public function __construct(string $username, string $email, string $password = '', string $confirmationPassword = '')
	{
		$this->username = $username;
		$this->email = $email;
		$this->password = $password;
		$this->confirmationPassword = $confirmationPassword;
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
