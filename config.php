<?php

//
// General database settings.
//
define('DB_HOSTNAME', 'localhost');
define('DB_NAME', 'legacy');
define('DB_USERNAME', 'legacy');
define('DB_PASSWORD', 'password');

//
// Names of the columns stored in various database tables.
//
define('DB_TABLE_USER', 'user');
define('DB_TABLE_USER_ID', 'id');
define('DB_TABLE_USER_NAME', 'username');
define('DB_TABLE_USER_EMAIL', 'email');
define('DB_TABLE_USER_PASSWORD', 'password');
define('DB_TABLE_USER_CAN_CREATE_POSTS', 'can_create_blog_posts');
define('DB_TABLE_USER_CAN_UPDATE_POSTS', 'can_update_blog_posts');
define('DB_TABLE_USER_CAN_DELETE_POSTS', 'can_delete_blog_posts');
define('DB_TABLE_BLOG_POST', 'post');
define('DB_TABLE_BLOG_POST_ID', 'id');
define('DB_TABLE_BLOG_POST_TITLE', 'title');
define('DB_TABLE_BLOG_POST_USER', 'author');
define('DB_TABLE_BLOG_POST_CONTENT', 'content');

//
// Path to the root of the system.
//
define('SITE_ROOT', __DIR__);

//
// Data limits for validation purposes.
//
define('DATA_USER_NAME_LENGTH', 24);
define('DATA_USER_EMAIL_LENGTH', 255);
define('DATA_USER_PASSWORD_MIN_LENGTH', 8);
define('DATA_USER_PASSWORD_MAX_LENGTH', 16);
define('DATA_BLOG_POST_TITLE_LENGTH', 32);
define('DATA_BLOG_POST_USER_LENGTH', 24);
define('DATA_BLOG_POST_CONTENT_LENGTH', 65535);

define('SYSTEM_DEBUGGING', 'true');
define('SYSTEM_LOG_SIZE', 4096);
define('SYSTEM_VERSION', 'N/a.');

?>
