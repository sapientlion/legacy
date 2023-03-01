<?php

require_once(__DIR__ . '/config.php');
require_once(SITE_ROOT . '/core/controllers/blog_controller.php');
require_once(SITE_ROOT . '/core/controllers/comment_controller.php');
require_once(SITE_ROOT . '/core/controllers/user_controller.php');

if (session_status() === PHP_SESSION_NONE) 
{
	session_start();
}

?>
<!DOCTYPE html>

<html>
<head>
	<link rel="stylesheet" href="themes/flashback/native.css" type="text/css">
	<link rel="stylesheet" href="themes/flashback/class.css" type="text/css">
	<link rel="stylesheet" href="themes/flashback/id.css" type="text/css">
	<title>Legacy | Home</title>
</head>

<body>
	<header>
		<header class="master" id="header-top">
			<?php

				$userController = new UserController();

				print(
					$userController->getHeader()
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
		$blogController = new BlogController(
			new BlogPost('', '', '')
		);

		$commentController = new CommentController(
			new Comment(
				(int)($_GET[GET_VAR_NAME_BLOG_POST]),
				'',
				''
				)
		);

		if(isset($_POST[BLOG_POST_ID_FIELD_NAME]) && !empty($_POST[BLOG_POST_ID_FIELD_NAME]))
		{
			print(
				$blogController->getViewForm(
					$_POST[BLOG_POST_ID_FIELD_NAME]
					)
			);

			print(
				$commentController->getCreationForm(
					$_POST[BLOG_POST_ID_FIELD_NAME]
					)
			);

			if(isset($_GET['from']) && !empty($_GET['from']))
			{
				$result = $commentController->getViewForms(
					$_GET['from']
				);
			}
			else
			{
				$result = $commentController->getViewForms();
			}
		}
		else
		{
			header('Location: /index.php');
		}

		?>

		<?php

			$commentController->getPageSelector();

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
