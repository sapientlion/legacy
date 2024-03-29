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

define('BLOG_ACTION_PATH', '/core/drivers/blog_driver.php');
define('BLOG_CREATION_PATH', '/core/drivers/blog_driver.php');
define('BLOG_VIEW_PATH', '/core/drivers/blog_driver.php?post=');
define('BLOG_UPDATE_PATH', '/core/drivers/blog_driver.php');
define('BLOG_REMOVAL_PATH', '/core/drivers/blog_driver.php');
define('BLOG_CREATION_PAGE_PATH', '/post_creator.php');
define('BLOG_VIEW_PAGE_PATH', '/post_reader.php');
define('BLOG_UPDATE_PAGE_PATH', '/post_updater.php');

define('COMMENT_ACTION_PATH', '/core/drivers/comment_driver.php');

?>
