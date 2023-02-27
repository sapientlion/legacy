<?php

require_once(__DIR__ . '/config.php');
require_once(SITE_ROOT . '/core/controllers/user_controller.php');
require_once(SITE_ROOT . '/core/controllers/blog_controller.php');

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

				print $userController->getHeader();

			?></header>

		<header class="master" id="header-middle">
			<h1>Legacy</h1>

			<header id="header-bottom">
				<nav><a href="index.php">Home</a></nav>
			</header>
		</header>
	</header>

	<div class="master workspace">
		<ul id="blog-post-list">
			<?php

				$blogController = new BlogController(
					new BlogPost('', '', '')
				);

				$result = array();

				if(isset($_GET['from']) && !empty($_GET['from']))
				{
					$result = $blogController->getViewForms($_GET['from']);
				}
				else
				{
					$result = $blogController->getViewForms();
				}

				
			?>
		</ul>

		<?php

			$blogController->getPageSelector();

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
