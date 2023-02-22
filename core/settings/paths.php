<?php

require_once(__DIR__ . '../../../config.php');
require_once(SITE_ROOT . '/core/settings/get.php');

define('USER_SIGNUP_PATH', '/core/drivers/user_driver.php?' . ACTION_NAME_USER_SIGNUP);
define('USER_UPDATE_PATH', '/core/drivers/user_driver.php?' . ACTION_NAME_USER_UPDATE);
define('USER_REMOVAL_PATH', '/core/drivers/user_driver.php?' . ACTION_NAME_USER_REMOVAL);
define('USER_SIGNIN_PATH', '/core/drivers/user_driver.php?' . ACTION_NAME_USER_SIGNIN);
define('USER_SIGNOUT_PATH', '/core/drivers/user_driver.php?' . ACTION_NAME_USER_SIGNOUT);
define('USER_SIGNUP_PAGE_PATH', '/user_signup.php');
define('USER_UPDATE_PAGE_PATH', '/user_updater.php');
define('USER_SIGNIN_PAGE_PATH', '/user_signin.php');

define('BLOG_CREATION_PATH', '/core/drivers/blog_driver.php?' . ACTION_NAME_BLOG_POST_CREATION);
define('BLOG_UPDATE_PATH', '/core/driver/blog_driver.php?' . ACTION_NAME_BLOG_POST_UPDATE);
define('BLOG_REMOVAL_PATH', '/core/driver/blog_driver.php?' . ACTION_NAME_BLOG_POST_REMOVAL);

?>
