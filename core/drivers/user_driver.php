<?php

require_once(__DIR__ . '../../../config.php');
require_once(SITE_ROOT . '/core/controllers/user_controller.php');
require_once(SITE_ROOT . '/core/models/user.php');
require_once(SITE_ROOT . '/core/settings/get.php');
require_once(SITE_ROOT . '/core/settings/input.php');
require_once(SITE_ROOT . '/core/settings/paths.php');
require_once(SITE_ROOT . '/core/settings/session.php');

class UserDriver
{
	private function checkCreateRequest() : bool
	{
		if(!isset($_POST[SIGNUP_USER_NAME_FIELD_NAME]) && empty($_POST[SIGNUP_USER_NAME_FIELD_NAME]))
		{
			return false;
		}

		if(!isset($_POST[SIGNUP_EMAIL_FIELD_NAME]) && empty($_POST[SIGNUP_EMAIL_FIELD_NAME]))
		{
			return false;
		}

		if(!isset($_POST[SIGNUP_PASSWORD_FIELD_NAME]) && empty($_POST[SIGNUP_PASSWORD_FIELD_NAME]))
		{
			return false;
		}

		if(!isset($_POST[SIGNUP_CONF_PASSWORD_FIELD_NAME]) && empty($_POST[SIGNUP_CONF_PASSWORD_FIELD_NAME]))
		{
			return false;
		}

		return true;
	}

	private function checkUpdateRequest() : bool
	{
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return false;
		}

		if(!isset($_SESSION[SESSION_VAR_NAME_USER_EMAIL]) && empty($_SESSION[SESSION_VAR_NAME_USER_EMAIL]))
		{
			return false;
		}

		if(!isset($_SESSION[SESSION_VAR_NAME_USER_PASSWORD]) && empty($_SESSION[SESSION_VAR_NAME_USER_EMAIL]))
		{
			return false;
		}

		return true;
	}

	private function checkDeleteRequest() : bool
	{
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return false;
		}

		return true;
	}

	private function checkEnterRequest() : bool
	{
		if(!isset($_POST[SIGNUP_USER_NAME_FIELD_NAME]) && empty($_POST[SIGNUP_USER_NAME_FIELD_NAME]))
		{
			return false;
		}

		if(!isset($_POST[SIGNUP_PASSWORD_FIELD_NAME]) && empty($_POST[SIGNUP_PASSWORD_FIELD_NAME]))
		{
			return false;
		}

		return true;
	}

	private function checkExitRequest() : bool
	{
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return false;
		}

		return true;
	}

	public function run() : bool
	{
		$username = '';
		$email = '';
		$password = '';
		$confirmationPassword = '';

		if(isset($_GET[ACTION_NAME_USER_SIGNUP]))
		{
			if(!$this->checkCreateRequest())
			{
				return false;
			}

			$username = $_POST[SIGNUP_USER_NAME_FIELD_NAME];
			$email = $_POST[SIGNUP_EMAIL_FIELD_NAME];
			$password = $_POST[SIGNUP_PASSWORD_FIELD_NAME];
			$confirmationPassword = $_POST[SIGNUP_CONF_PASSWORD_FIELD_NAME];
		}

		if(isset($_GET[ACTION_NAME_USER_UPDATE]))
		{
			if(!$this->checkUpdateRequest())
			{
				return false;
			}

			$username = $_SESSION[SESSION_VAR_NAME_USER_NAME];
			$email = $_SESSION[SESSION_VAR_NAME_USER_EMAIL];
			$password = $_SESSION[SESSION_VAR_NAME_USER_PASSWORD];
		}

		if(isset($_GET[ACTION_NAME_USER_REMOVAL]))
		{
			if(!$this->checkDeleteRequest())
			{
				return false;
			}

			$username = $_SESSION[SESSION_VAR_NAME_USER_NAME];
		}

		if(isset($_GET[ACTION_NAME_USER_SIGNIN]))
		{
			if(!$this->checkEnterRequest())
			{
				return false;
			}

			$username = $_POST[SIGNUP_USER_NAME_FIELD_NAME];
			$password = $_POST[SIGNUP_PASSWORD_FIELD_NAME];
		}

		if(isset($_GET[ACTION_NAME_USER_SIGNOUT]))
		{
			if(!$this->checkExitRequest())
			{
				return false;
			}

			$username = $_POST[SIGNUP_USER_NAME_FIELD_NAME];
			$password = $_POST[SIGNUP_PASSWORD_FIELD_NAME];
		}

		if(isset($_GET[ACTION_NAME_USER_SIGNUP])) 
		{
			$userController = new UserController(new User(
				$username,
				$email,
				$password,
				$confirmationPassword)
			);

			$userController->create();
			header('Location: ' . USER_SIGNIN_PAGE_PATH);
		}

		if(isset($_GET[ACTION_NAME_USER_UPDATE])) 
		{
			if(empty($_POST[SIGNUP_PASSWORD_FIELD_NAME]))
			{
				$userController = new UserController(new User(
					$_POST[SIGNUP_USER_NAME_FIELD_NAME],
					$_POST[SIGNUP_EMAIL_FIELD_NAME])
				);

				$userController->update(
					0,
					$_SESSION[SESSION_VAR_NAME_USER_NAME]
				);
			}
			else
			{
				$userController = new UserController(new User(
					$_POST[SIGNUP_USER_NAME_FIELD_NAME],
					$_POST[SIGNUP_EMAIL_FIELD_NAME],
					$_POST[SIGNUP_PASSWORD_FIELD_NAME],
					$_POST[SIGNUP_CONF_PASSWORD_FIELD_NAME])
				);

				$userController->update(
					1,
					$_SESSION[SESSION_VAR_NAME_USER_NAME]
				);
			}


			header('Location: /user_updater.php');
		}

		if(isset($_GET[ACTION_NAME_USER_REMOVAL])) 
		{
			$userController = new UserController(new User(
				$username,
				$email)
			);

			$userController->delete(
				$_SESSION[SESSION_VAR_NAME_USER_NAME]
			);
			$userController->signOut();
			header('Location: /index.php');
		}

		if(isset($_GET[ACTION_NAME_USER_SIGNIN])) 
		{
			$userController = new UserController(new User(
				$username,
				$email,
				$password)
			);

			$userController->signIn();
			header('Location: /index.php');
		}

		if(isset($_GET[ACTION_NAME_USER_SIGNOUT])) 
		{
			$userController = new UserController();

			$userController->signOut();
			header('Location: /index.php');
		}

		return true;
	}
}

$userDriver = new UserDriver();
$userDriver->run();

?>
