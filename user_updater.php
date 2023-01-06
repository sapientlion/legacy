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

		if(isset($_SESSION['UserName']) && !empty($_SESSION['UserName'])
		&& isset($_SESSION['Email']) && !empty($_SESSION['Email']))
		{
		    print '<form action="core/controllers/user_controller.php?update" method="post">
            <label for="username">Username:</label><br>
            <input type="text" id="username" 
			oninput="userController.check()" name="username" value=' . $_SESSION['UserName'] . '><br>
			<div id="messagebox-username" class="messagebox"></div>

            <label for="email">E-mail:</label><br>
            <input type="email" id="email" name="email" 
			oninput="userController.check()" value=' . $_SESSION['Email'] . '><br>
			<div id="messagebox-email" class="messagebox"></div>

            <label for="password">Old Password:</label><br>
            <input type="password" id="password" name="password" 
			oninput="userController.check()"><br>
			<div id="messagebox-password" class="messagebox"></div>

			<label for="confirmation-password">New Password:</label><br>
            <input type="password" id="confirmation-password" name="confirmation-password" 
			oninput="userController.check()"><br>
			<div id="messagebox-confirmation-password" class="messagebox"></div>

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
