<?php

interface IUserFrontend
{
	public function getHeader() : string;
	public function getSignupForm() : string;
	public function getUpdateForm() : string;
	public function getSigninForm() : string;
}

?>
