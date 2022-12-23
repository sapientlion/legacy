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
	<script src="core/js/user_validator.js"></script>
	<body onload="userController.check()">
		<header>
			<a href="index.php">Home</a>

			<?php

			$userController = new UserController();

			print $userController->genSiteHeader();

			?>
		</header>

		<?php

		if(!isset($_SESSION['username']) || empty($_SESSION['username']))
		{
			print '<form action="core/controllers/user_controller.php?signin" method="post">
            <label for="username">Username:</label><br>
            <input type="text" id="username" 
			oninput="userController.check()" name="username"><br>
			<div id="messagebox-username" class="messagebox"></div>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" 
			oninput="userController.check()"><br>
			<div id="messagebox-password" class="messagebox"></div>

            <input type="submit" value="Sign in" id="submission-button">
        	</form>';
		}
		else
		{
			header('Location: ' . SITE_ROOT);
		}

		?>
    </body>
</html>
