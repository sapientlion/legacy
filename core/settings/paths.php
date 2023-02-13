<?php

require_once(__DIR__ . '../../../config.php');
require_once(SITE_ROOT . '/core/settings/get.php');

define('USER_SIGNUP_PATH', '/core/controllers/user_controller.php?' . ACTION_NAME_USER_SIGNUP);
define('USER_UPDATE_PATH', '/core/controllers/user_controller.php?' . ACTION_NAME_USER_UPDATE);
define('USER_REMOVAL_PATH', '/core/controllers/user_controller.php?' . ACTION_NAME_USER_REMOVAL);
define('USER_SIGNIN_PATH', '/core/controllers/user_controller.php?' . ACTION_NAME_USER_SIGNIN);
define('USER_SIGNOUT_PATH', '/core/controllers/user_controller.php?' . ACTION_NAME_USER_SIGNOUT);
define('USER_SIGNUP_PAGE_PATH', '/user_signup.php');
define('USER_UPDATE_PAGE_PATH', '/user_updater.php');
define('USER_SIGNIN_PAGE_PATH', '/user_signin.php');

?>
