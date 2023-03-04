<?php

if(session_status() === PHP_SESSION_NONE)
{
	session_start();
}

require_once(__DIR__ . '/../../config.php');
require_once(SITE_ROOT . '/core/interfaces/iuser_frontend.php');
require_once(SITE_ROOT . '/core/settings/get.php');
require_once(SITE_ROOT . '/core/settings/input.php');
require_once(SITE_ROOT . '/core/settings/paths.php');
require_once(SITE_ROOT . '/core/settings/session.php');

class UserFrontend implements IUserFrontend
{
	/**
	 * Get a control panel.
	 *
	 * @return string a top header which contains various user data such as user name, avatar and et cetera.
	*/
	public function getHeader() : string
	{
		$header = '';

		if(isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && !empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			$header = '<a href="' . BLOG_CREATION_PAGE_PATH . '">Create Post</a>
			<a href="' . USER_UPDATE_PAGE_PATH . '">' . $_SESSION[SESSION_VAR_NAME_USER_NAME] . '</a>
			<a href="' . USER_SIGNOUT_PATH . '">Sign out</a>';

			return $header;
		}

		$header = '<a href="' . USER_SIGNUP_PAGE_PATH . '">Sign up</a>
		<a href="' . USER_SIGNIN_PAGE_PATH . '">Sign in</a>';

		return $header;
	}

	/**
	 * Get a user signup form.
	 *
	 * @return string a signup form on success and an empty string on failure.
	*/
	public function getSignupForm() : string
	{
		$form = '<form class="user-form" action="' . USER_SIGNUP_PATH . '" method="post">
			<div class="user-form-labels">
				<label for="' . SIGNUP_USER_NAME_FIELD_NAME . '">Username:</label><br>
				<label for="' . SIGNUP_EMAIL_FIELD_NAME . '">E-mail:</label><br>
				<label for="' . SIGNUP_PASSWORD_FIELD_NAME . '">New Password:</label><br>
				<label for="' . SIGNUP_CONF_PASSWORD_FIELD_NAME . '">Confirm Password:</label><br>
			</div>

			<div class="user-form-fields">
				<input type="text" id="' . SIGNUP_USER_NAME_FIELD_NAME . '" name="' . SIGNUP_USER_NAME_FIELD_NAME . '"><br>

				<input type="email" id="' . SIGNUP_EMAIL_FIELD_NAME . '" name="' . SIGNUP_EMAIL_FIELD_NAME . '"><br>
		
				<input type="password" id="' . SIGNUP_PASSWORD_FIELD_NAME . '" name="' . SIGNUP_PASSWORD_FIELD_NAME . '"><br>
	
				<input type="password" id="' . SIGNUP_CONF_PASSWORD_FIELD_NAME . '" name="' . 
				SIGNUP_CONF_PASSWORD_FIELD_NAME . '"><br>
			</div>
		
			<div class="user-form-controller">
				<button type="submit" value="Sign up" id="submission-button">Sign up</button>
			</div>
		
		</form>';

		//
		// Signed-in user is prohibited from creating a new account.
		//
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) || empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return $form;
		}
		
		header('Location: ' . SITE_ROOT);

		return '';
	}

	/**
	 * Get update form.
	 *
	 * @return string user account update form on success and an amepty string on failure.
	*/
	public function getUpdateForm() : string
	{
		$form = '<form class="user-form" action="' . USER_UPDATE_PATH . '" method="post">
			<div class="user-form-labels">
				<label for="' . SIGNUP_USER_NAME_FIELD_NAME . '">Username:</label><br>
				<label for="' . SIGNUP_EMAIL_FIELD_NAME . '">E-mail:</label><br>
				<label for="' . SIGNUP_PASSWORD_FIELD_NAME . '">New Password:</label><br>
				<label for="' . SIGNUP_CONF_PASSWORD_FIELD_NAME . '">Confirm Password:</label><br>
			</div>
		
			<div class="user-form-fields">
				<input type="text" id="' . SIGNUP_USER_NAME_FIELD_NAME . '" name="' . 
				SIGNUP_USER_NAME_FIELD_NAME . '" value=' . $_SESSION[SESSION_VAR_NAME_USER_NAME] . '><br>

				<input type="email" id="' . SIGNUP_EMAIL_FIELD_NAME . '" name="' . 
				SIGNUP_EMAIL_FIELD_NAME . '" value=' . $_SESSION[SESSION_VAR_NAME_USER_EMAIL] . '><br>

				<input type="password" id="' . SIGNUP_PASSWORD_FIELD_NAME . '" name="' . 
				SIGNUP_PASSWORD_FIELD_NAME . '"><br>

				<input type="password" id="' . SIGNUP_CONF_PASSWORD_FIELD_NAME . '" name="' .
				SIGNUP_CONF_PASSWORD_FIELD_NAME . '"><br>
			</div>

			<div class="user-form-controller">
				<button type="submit" value="Update" id="submission-button">Update</button>	
			</div>
		</form>';

		//
		// TODO don't forget to implement "new password" field when the time is right.
		//
		/*$form = '<form action="' . USER_UPDATE_PATH . '" method="post">
		<label for="' . SIGNUP_USER_NAME_FIELD_NAME . '">Username:</label><br>
		<input type="text" id="' . SIGNUP_USER_NAME_FIELD_NAME . '" name="' . 
		SIGNUP_USER_NAME_FIELD_NAME . '" value=' . $_SESSION[SESSION_VAR_NAME_USER_NAME] . '><br>

		<label for="' . SIGNUP_EMAIL_FIELD_NAME . '">E-mail:</label><br>
		<input type="email" id="' . SIGNUP_EMAIL_FIELD_NAME . '" name="' . 
		SIGNUP_EMAIL_FIELD_NAME . '" value=' . $_SESSION[SESSION_VAR_NAME_USER_EMAIL] . '><br>

		<label for="' . SIGNUP_PASSWORD_FIELD_NAME . '">Old Password:</label><br>
		<input type="password" id="' . SIGNUP_PASSWORD_FIELD_NAME . '" name="' . 
		SIGNUP_PASSWORD_FIELD_NAME . '"><br>

		<label for="' . SIGNUP_NEW_PASSWORD_FIELD_NAME . '">New Password:</label><br>
		<input type="password" id="' . SIGNUP_NEW_PASSWORD_FIELD_NAME . '" name="' . 
		SIGNUP_NEW_PASSWORD_FIELD_NAME . '"><br>

		<label for="' . SIGNUP_CONF_PASSWORD_FIELD_NAME . '">Confirm Password:</label><br>
		<input type="password" id="' . SIGNUP_CONF_PASSWORD_FIELD_NAME . '" name="' .
		SIGNUP_CONF_PASSWORD_FIELD_NAME . '"><br>

		<button type="submit" value="Update" id="submission-button">Update</button>
		</form>';*/

		//
		// User must be signed-in first in order to update their data.
		//
		if(isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && !empty($_SESSION[SESSION_VAR_NAME_USER_NAME]) && 
		isset($_SESSION[SESSION_VAR_NAME_USER_EMAIL]) && !empty($_SESSION[SESSION_VAR_NAME_USER_EMAIL]))
		{
			return $form;
		}

		header('Location: ' . SITE_ROOT);

		return '';
	}

	/**
	 * Get a user signin form.
	 *
	 * @return string user signin form on success and an empty string on failure.
	*/
	public function getSigninForm() : string
	{
		$form = '<form class="user-form" action="' . USER_SIGNIN_PATH . '" method="post">
			<div class="user-form-labels">
				<label for="' . SIGNUP_USER_NAME_FIELD_NAME . '">Username:</label><br>
				<label for="' . SIGNUP_PASSWORD_FIELD_NAME . '">Password:</label><br>
			</div>

			<div class="user-form-fields">
				<input type="text" id="' . SIGNUP_USER_NAME_FIELD_NAME . '" name="' . SIGNUP_USER_NAME_FIELD_NAME . '"><br>
				<input type="password" id="' . SIGNUP_PASSWORD_FIELD_NAME . '" name="' . SIGNUP_PASSWORD_FIELD_NAME . '" ><br>
			</div>

			<div class="user-form-controller">
				<button type="submit" value="Sign in" id="submission-button">Sign in</button>
			</div>
		</form>';

		//
		// Signed-in user is prohibited from signing in again (that doesn't make any sense).
		//
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) || empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return $form;
		}

		//
		// Return to the home page.
		//
		header('Location: ' . SITE_ROOT);

		return '';
	}
}

?>
