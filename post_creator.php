<?php

if(session_status() === PHP_SESSION_NONE)
{
	session_start();
}

require_once(__DIR__ . '/config.php');
require_once(SITE_ROOT . '/core/controllers/user_controller.php');

?>

<!DOCTYPE html>

<html>
	<body">
		<header>
			<a href="index.php">Home</a>

			<?php

			$userController = new UserController();

			print $userController->genSiteHeader();

			?>
		</header>

		<?php

		if(isset($_SESSION['UserName']) && !empty($_SESSION['UserName'])) 
		{
			print '<form action="core/controllers/blog_controller.php?create" method="post">
			<label for="title">Title:</label><br>
			<input type="text" id="title" name="title"><br>
			<div id="messagebox-title" class="messagebox"></div>
	
			<label for="author">Author:</label><br>
			<input type="text" id="author" name="author"><br>
			<div id="messagebox-author" class="messagebox"></div>
	
			<label for="content">Content:</label><br>
			<input type="text" id="content" name="content">br>
			<div id="messagebox-content" class="messagebox"></div>
	
			<input type="submit" value="Post" id="submission-button">
			</form>';
		}
		else
		{
			header('Location: ' . SITE_ROOT);
		}

		?>
    </body>
</html>
