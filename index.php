<?php

require_once(__DIR__ . '/config.php');
require_once(SITE_ROOT . '/core/controllers/user_controller.php');
require_once(SITE_ROOT . '/core/controllers/blog_controller.php');

if (session_status() === PHP_SESSION_NONE) 
{
	session_start();
}

if (isset($_GET['page']) && !empty($_GET['page'])) 
{
	$_SESSION['RowFrom'] = (int)$_GET['page'] * 10;
	$_SESSION['RowTo'] = ((int)$_GET['page'] * 10) + 10;
}

$_SESSION['RowFrom'] = 0;
$_SESSION['RowTo'] = 10;

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

				print $userController->genSiteHeader();

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
				$result = $blogController->readAll((int)$_SESSION['RowFrom'], (int)$_SESSION['RowTo']);

				if (count($result) > 0) 
				{
					if(!isset($_SESSION['UserName']) || empty($_SESSION['UserName']))
					{
						foreach ($result as $post) 
						{
							$string = '<form class="master blog-post" action="core/controllers/blog_controller.php" method="post">
							<input class="hidden" type="text" id="post-' . $post[0] . '" name="id" value="' . $post[0] . '" readonly><br>
							
							<div class="blog-post-row">
							<input type="text" id="title" name="title" value="Title: ' . $post[1] . '" readonly><br>
							</div>
							
							<div class="blog-post-row">
							<input type="text" id="author" name="author" value="Author: ' . $post[2] . '" readonly><br>
							</div>
							
							<input type="text" id="content" name="content" value="' . $post[3] . '" readonly><br>
							
							<div class="blog-post-controller">
							<button type="submit" name="action" value="read">Read</button>
							</div>
							
							</form>';
							
							print $string;
						}
					}
					else
					{
						foreach ($result as $post) 
						{
							$string = '<form class="master blog-post" action="core/controllers/blog_controller.php" method="post">
							<input class="hidden" type="text" id="post-' . $post[0] . '" name="id" value="' . $post[0] . '" readonly><br>
							
							<div class="blog-post-row">
							<input type="text" id="title" name="title" value="Title: ' . $post[1] . '" readonly><br>
							</div>
							
							<div class="blog-post-row">
							<input type="text" id="author" name="author" value="Author: ' . $post[2] . '" readonly><br>
							</div>
															
							<input type="text" id="content" name="content" value="' . $post[3] . '" readonly><br>
							
							<div class="blog-post-controller">
							<button type="submit" name="action" value="read">Read</button>
							<button type="submit" name="action" value="update">Update</button>
							<button type="submit" name="action" value="delete">Delete</button>
							</div>
							
							</form>';
															
							print $string;
						}
					}
				}

				$rownum = $blogController->getRowNum();
				$pagenum = 1;

				if ($rownum > 10)
				{
					print '<a href="index.php?page="' . $pagenum . '">' . $pagenum . '</a>';
					print '<a href="index.php?page="' . $pagenum++ . '">' . $pagenum . '</a>';
					
					for ($index = 11; $index !== $rownum; $index++) 
					{
						if ($index % 10 === 0 && $index < $rownum) 
						{
							print '<a href="index.php?page=' . $pagenum++ . '">' . $pagenum . '</a>';
						}
					}
				}

			?>

		</ul>
	</div>

	<footer class="master">
		<footer id="footer-top">
			<h6>Copyright © 2021 - <?php print date('Y') ?> Legacy. All rights reserved.</h6>
		</footer>

		<footer id="footer-middle"></footer>

		<footer id="footer-bottom">
			<h6><?php print SYSTEM_VERSION ?></h6>
		</footer>
	</footer>
</body>
</html>
