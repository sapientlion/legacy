<?php

require_once(__DIR__ . '../../../config.php');
require_once(SITE_ROOT . '/core/controllers/user_controller.php');
require_once(SITE_ROOT . '/core/models/user.php');
require_once(SITE_ROOT . '/core/settings/get.php');
require_once(SITE_ROOT . '/core/settings/input.php');
require_once(SITE_ROOT . '/core/settings/paths.php');
require_once(SITE_ROOT . '/core/settings/session.php');

class UserDriver implements IUserDriver
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

	private function create(array $userData) : bool
	{
		$userController = new UserController(new User(
			$userData[SIGNUP_USER_NAME_FIELD_NAME],
			$userData[SIGNUP_EMAIL_FIELD_NAME],
			$userData[SIGNUP_PASSWORD_FIELD_NAME],
			$userData[SIGNUP_CONF_PASSWORD_FIELD_NAME])
		);

		$result = $userController->create();

		return $result;
	}

	private function update(array $userData) : bool
	{
		$result = false;

		if(empty($userData[SIGNUP_PASSWORD_FIELD_NAME]))
		{
			$userController = new UserController(new User(
				$userData[SIGNUP_USER_NAME_FIELD_NAME],
				$userData[SIGNUP_EMAIL_FIELD_NAME])
			);

			$result = $userController->update(
				0,
				$_SESSION[SESSION_VAR_NAME_USER_NAME]
			);
		}
		else
		{
			$userController = new UserController(new User(
				$userData[SIGNUP_USER_NAME_FIELD_NAME],
				$userData[SIGNUP_EMAIL_FIELD_NAME],
				$userData[SIGNUP_PASSWORD_FIELD_NAME],
				$userData[SIGNUP_CONF_PASSWORD_FIELD_NAME])
			);

			$result = $userController->update(
				1,
				$_SESSION[SESSION_VAR_NAME_USER_NAME]
			);
		}

		return $result;
	}

	private function delete(array $userData) : bool
	{
		$userController = new UserController(new User(
			$userData[SESSION_VAR_NAME_USER_NAME],
			$userData[SESSION_VAR_NAME_USER_EMAIL])
		);

		$result = $userController->delete(
			$_SESSION[SESSION_VAR_NAME_USER_NAME]
		);

		if(!$result)
		{
			return $result;
		}

		$result = $userController->signOut();

		return $result;
	}

	private function enter(array $userData) : bool
	{
		$userController = new UserController(new User(
			$userData[SIGNUP_USER_NAME_FIELD_NAME],
			$userData[SIGNUP_USER_NAME_FIELD_NAME],
			$userData[SIGNUP_PASSWORD_FIELD_NAME])
		);

		$result = $userController->signIn();

		return $result;
	}

	private function exit() : bool
	{
		$userController = new UserController();

		$result = $userController->signOut();

		return $result;
	}

	public function run() : bool
	{
		if(isset($_GET[ACTION_NAME_USER_SIGNUP]))
		{
			if(!$this->checkCreateRequest())
			{
				return false;
			}

			$userData = [
				SIGNUP_USER_NAME_FIELD_NAME => $_POST[SIGNUP_USER_NAME_FIELD_NAME],
				SIGNUP_EMAIL_FIELD_NAME => $_POST[SIGNUP_EMAIL_FIELD_NAME],
				SIGNUP_PASSWORD_FIELD_NAME => $_POST[SIGNUP_PASSWORD_FIELD_NAME],
				SIGNUP_CONF_PASSWORD_FIELD_NAME => $_POST[SIGNUP_CONF_PASSWORD_FIELD_NAME]
			];

			$result = $this->create($userData);

			header('Location: ' . USER_SIGNIN_PAGE_PATH);

			return $result;
		}

		if(isset($_GET[ACTION_NAME_USER_UPDATE]))
		{
			if(!$this->checkUpdateRequest())
			{
				return false;
			}

			$userData = [
				SIGNUP_USER_NAME_FIELD_NAME => $_POST[SIGNUP_USER_NAME_FIELD_NAME],
				SIGNUP_EMAIL_FIELD_NAME => $_POST[SIGNUP_EMAIL_FIELD_NAME],
				SIGNUP_PASSWORD_FIELD_NAME => $_POST[SIGNUP_PASSWORD_FIELD_NAME],
			];

			$result = $this->update($userData);
			
			header('Location: ' . USER_UPDATE_PAGE_PATH);

			return $result;
		}

		if(isset($_GET[ACTION_NAME_USER_REMOVAL]))
		{
			if(!$this->checkDeleteRequest())
			{
				return false;
			}

			$userData = [
				SESSION_VAR_NAME_USER_NAME => $_SESSION[SESSION_VAR_NAME_USER_NAME],
				SESSION_VAR_NAME_USER_EMAIL => $_SESSION[SESSION_VAR_NAME_USER_EMAIL]
			];

			$result = $this->delete($userData);

			header('Location: ' . USER_SIGNIN_PAGE_PATH);

			return $result;
		}

		if(isset($_GET[ACTION_NAME_USER_SIGNIN]))
		{
			if(!$this->checkEnterRequest())
			{
				return false;
			}

			$userData = [
				SIGNUP_USER_NAME_FIELD_NAME => $_POST[SIGNUP_USER_NAME_FIELD_NAME],
				SIGNUP_PASSWORD_FIELD_NAME => $_POST[SIGNUP_PASSWORD_FIELD_NAME]
			];

			$result = $this->enter($userData);

			if(!$result)
			{
				header('Location: ' . USER_SIGNIN_PAGE_PATH);

				return $result;
			}

			header('Location: /index.php');

			return $result;
		}

		if(isset($_GET[ACTION_NAME_USER_SIGNOUT]))
		{
			if(!$this->checkExitRequest())
			{
				return false;
			}

			$result = $this->exit();

			header('Location: /index.php');

			return $result;
		}

		return true;
	}
}

$userDriver = new UserDriver();
$userDriver->run();

?>
