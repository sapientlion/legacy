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

		if(isset($_POST['id']) && !empty($_POST['id']) ||
		isset($_POST['title']) && !empty($_POST['title']) || 
		isset($_POST['author']) && !empty($_POST['author']) ||
		isset($_POST['content']) && !empty($_POST['content']) && $_POST['author'] === $_SESSION['UserName'])
		{
			print '<form action="core/controllers/blog_controller.php?update" method="post">
			<input type="text" id="post-' . $_POST['id'] . '" name="id" value="' . $_POST['id'] . '" readonly hidden><br>

            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title"  value="' . $_POST['title'] .'"><br>
			<div id="messagebox-title" class="messagebox"></div>

            <label for="author">Author:</label><br>
            <input type="text" id="author" name="author" value="' . $_POST['author'] . '" disabled><br>
			<div id="messagebox-author" class="messagebox"></div>

            <label for="content">Content:</label><br>
            <input type="text" id="content" name="content" value="' . $_POST['content'] . '">br>
			<div id="messagebox-content" class="messagebox"></div>

            <input type="submit" value="Update" id="submission-button">
        	</form>';
		}
		else
		{
			header('Location: ' . SITE_ROOT);
		}

		?>
    </body>
</html>
