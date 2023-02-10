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
<head>
	<link rel="stylesheet" href="themes/flashback/native.css" type="text/css">
	<link rel="stylesheet" href="themes/flashback/class.css" type="text/css">
	<link rel="stylesheet" href="themes/flashback/id.css" type="text/css">
	<script src="core/js/user_validator.js" type="text/javascript">
</script>

	<title>Legacy | Signup</title>
</head>

<body onload="userController.check()">
	<header>
		<header class="master" id="header-top">
			<?php

				$userController = new UserController();

				print $userController->genSiteHeader();

			?>
		</header>

		<header class="master" id="header-middle">
			<h1>Legacy</h1>

			<header id="header-bottom">
				<nav><a href="index.php">Home</a></nav>
			</header>
		</header>
	</header>

	<div class="master workspace">
	
	<?php

		if(!isset($_SESSION['UserName']) || empty($_SESSION['UserName']))
		{
			print '<form action="core/controllers/user_controller.php?signup" method="post">
			<label for="username">Username:</label><br>
			<input type="text" id="username" 
			oninput="userController.check()" name="username"><br>
			<div id="messagebox-username" class="messagebox"></div>

			<label for="email">E-mail:</label><br>
			<input type="email" id="email" name="email" 
			oninput="userController.check()"><br>
			<div id="messagebox-email" class="messagebox"></div>
			
			<label for="password">Password:</label><br>
			<input type="password" id="password" name="password" 
			oninput="userController.check()"><br>
			<div id="messagebox-password" class="messagebox"></div>
		
			<label for="confirmation-password">Confirm Password:</label><br>
			<input type="password" id="confirmation-password" name="confirmation-password" 
			oninput="userController.check()"><br>
			<div id="messagebox-confirmation-password" class="messagebox"></div>
		
			<button type="submit" value="Sign up" id="submission-button">Sign up</button>
			</form>';
		}
		else
		{
			header('Location: ' . SITE_ROOT);
		}

	?>

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
