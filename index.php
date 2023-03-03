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
	<link rel="stylesheet" href="themes/flashback/native.css" type="text/css">
	<link rel="stylesheet" href="themes/flashback/class.css" type="text/css">
	<link rel="stylesheet" href="themes/flashback/id.css" type="text/css">
	<title>Legacy | Home</title>
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

	<form class="master search-bar">
		<input type="text" id="search-bar-input" name="search-bar-input"><br>

		<select id="search-bar-filter" name="search-bar-filter">
			<option value="title">by title</option>
			<option value="author">by author</option>
		</select>

		<button type="submit" value="search" id="submission-button">Search</button>	
	</form>

	<div class="master workspace">
		<ul id="blog-posts">
			<?php

				$blogFrontend = new BlogFrontend(
					new BlogPost(
						'', 
						'', 
						'')
				);

				$result = array();

				if(isset($_GET['from']) && !empty($_GET['from']))
				{
					$result = $blogFrontend->getPosts(
						$_GET['from']
					);
				}
				else
				{
					$result = $blogFrontend->getPosts();
				}

				
			?>
		</ul>

		<?php

			$blogFrontend->getPageSelector();
			
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
