<?php

require_once(__DIR__ . '/config.php');
require_once(SITE_ROOT . '/core/frontends/user_frontend.php');
require_once(SITE_ROOT . '/core/frontends/blog_frontend.php');

if (session_status() === PHP_SESSION_NONE) 
{
	session_start();
}

?>
<!DOCTYPE html>

<html>
<head>
	<link rel="stylesheet" href="themes/flashback/blog_post.css" type="text/css">
	<link rel="stylesheet" href="themes/flashback/native.css" type="text/css">
	<link rel="stylesheet" href="themes/flashback/class.css" type="text/css">
	<link rel="stylesheet" href="themes/flashback/id.css" type="text/css">
	<title>Legacy | Post Updater</title>
</head>

<body>
	<header>
		<header class="master" id="header-top">
			<?php

				$userFrontend = new UserFrontend();

				print(
					$userFrontend->getHeader()
				);

			?>
		</header>

		<header class="master" id="header-middle">
			<h1>Legacy</h1>
		</header>

		<header class="master" id="header-bottom">
				<nav><a href="index.php">Home</a></nav>
		</header>
	</header>

	<div class="master workspace">
		<?php
		
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			header('Location: /index.php');
		}
		else
		{
			if(isset($_POST[BLOG_POST_ID_FIELD_NAME]) && !empty($_POST[BLOG_POST_ID_FIELD_NAME]))
			{
				$blogFrontend = new BlogFrontend(
					new BlogPost(
						'',
						'',
						'',
						$_POST[BLOG_POST_ID_FIELD_NAME]
					)
				);

				$result = $blogFrontend->getEditor(
					$_POST[BLOG_POST_ID_FIELD_NAME]
				);

				print($result);
			}
			else
			{
				header('Location: /index.php');
			}
		}

		?>
	</div>

	<footer class="master">
		<footer id="footer-top">
			<h6>Copyright © 2021 - <?php print(date('Y')) ?> Legacy. All rights reserved.</h6>
		</footer>

		<footer id="footer-middle"></footer>

		<footer id="footer-bottom">
			<h6><?php print(SYSTEM_VERSION) ?></h6>
		</footer>
	</footer>
</body>
</html>
