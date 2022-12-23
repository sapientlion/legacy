<?php

require_once(__DIR__ . '/config.php');
require_once(SITE_ROOT . '/core/controllers/user_controller.php');
require_once(SITE_ROOT . '/core/controllers/blog_controller.php');

if(session_status() === PHP_SESSION_NONE)
{
	session_start();
}

if(isset($_GET['page']) && !empty($_GET['page']))
{
	$_SESSION['RowFrom'] = (int)$_GET['page'] * 10;
	$_SESSION['RowTo'] = ((int)$_GET['page'] * 10) + 10;
}

$_SESSION['RowFrom'] = 0;
$_SESSION['RowTo'] = 10;

?>

<!DOCTYPE html>

<html>
	<body>
		<header>
			<a href="index.php">Home</a>

			<?php

			$userController = new UserController();

			print $userController->genSiteHeader();

			?>
		</header>

		<?php

		$blogController = new BlogController();
		$result = $blogController->readAll((int)$_SESSION['RowFrom'], (int)$_SESSION['RowTo']);

		if(count($result) > 0)
		{
			foreach($result as $post)
			{
				print '<form action="core/controllers/blog_controller.php?readall" method="post">
				<input type="text" id="post-' . $post[0] . '" name="id" value="' . $post[0] . '" readonly hidden><br>

				<label for="title">Title:</label><br>
				<input type="text" id="title" name="title" value="' . $post[1] . '" readonly><br>
	
				<label for="author">Author:</label><br>
				<input type="text" id="author" name="author" value="' . $post[2] . '" readonly><br>
	
				<label for="content">Content:</label><br>
				<input type="text" id="content" name="content" value="' . $post[3] . '" readonly><br>

				<input type="submit" value="Post" id="submission-button">
				</form>';
			}
		}

		$rownum = $blogController->getRowNum();
		$pagenum = 1;

		if($rownum > 10)
		{
			print '<a href="index.php?page="' . $pagenum . '">' . $pagenum . '</a>';
			print '<a href="index.php?page="' . $pagenum++ . '">' . $pagenum . '</a>';

			for($index = 11; $index !== $rownum; $index++)
			{
				if($index % 10 === 0 && $index < $rownum)
				{
					print '<a href="index.php?page=' . $pagenum++ . '">' . $pagenum . '</a>';
				}
			}
		}

		?>
    </body>
</html>
