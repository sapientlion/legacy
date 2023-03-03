<?php

if(session_status() === PHP_SESSION_NONE)
{
	session_start();
}

require_once(__DIR__ . '/../../config.php');
require_once(SITE_ROOT . '/core/controllers/comment_controller.php');
require_once(SITE_ROOT . '/core/interfaces/icomment_frontend.php');
require_once(SITE_ROOT . '/core/settings/get.php');
require_once(SITE_ROOT . '/core/settings/input.php');
require_once(SITE_ROOT . '/core/settings/paths.php');
require_once(SITE_ROOT . '/core/settings/session.php');

class CommentFrontend extends CommentController implements ICommentFrontend
{
	public function getCreator() : string
	{
		if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
		{
			return '';
		}

		$form = '<div class="master" id="comment-creator">
			<form method="post">

			<input class="hidden" type="text" id="' . 
			COMMENT_POST_ID_FIELD_NAME . '-' . $this->comment->postID . '" name="' . 
			COMMENT_POST_ID_FIELD_NAME . '" value="' . $this->comment->postID . '" readonly><br>

			<input class="hidden" type="text" id="' . 
			COMMENT_AUTHOR_FIELD_NAME . '" name="' . 
			COMMENT_AUTHOR_FIELD_NAME . '" value="' . 
			$_SESSION[SESSION_VAR_NAME_USER_NAME] . '" readonly><br>

			<textarea id="' . 
			COMMENT_CONTENT_FIELD_NAME . '" name="' . 
			COMMENT_CONTENT_FIELD_NAME . '" rows="15" cols="150"></textarea><br>
		
			<button type="submit" formaction="' . 
			COMMENT_ACTION_PATH . '" name="' . 
			COMMENT_SUBMIT_BUTTON_NAME . '" value="' . 
			ACTION_NAME_COMMENT_CREATION . '">Comment</button>
		</div>

		</form>';

		return $form;
	}

	public function getComments(int $from = 0) : array
	{
		$result = $this->readAll();
		$totalComments = count($result);

		if($totalComments > 5)
		{
			$result = array_slice($result, $from, 5);
		}

		if($totalComments > 0)
		{
			if(!isset($_SESSION[SESSION_VAR_NAME_USER_NAME]) && empty($_SESSION[SESSION_VAR_NAME_USER_NAME]))
			{
				foreach ($result as $post) 
				{
					$comment = '<form class="master comment" method="post">
					<input class="hidden" type="text" id="' . COMMENT_ID_FIELD_NAME . '-' . $post[DB_TABLE_COMMENT_ID] . '" name="' . COMMENT_ID_FIELD_NAME . '" value="' . $post[DB_TABLE_COMMENT_ID] . '" readonly><br>

					<input class="hidden" type="text" id="' . COMMENT_POST_ID_FIELD_NAME . '-' . $post[DB_TABLE_COMMENT_POST_ID] . '" name="' . COMMENT_POST_ID_FIELD_NAME . '" value="' . $post[DB_TABLE_COMMENT_POST_ID] . '" readonly><br>
					
					<div class="comment-left">
						<textarea id="' . COMMENT_CONTENT_FIELD_NAME . '" name="' . COMMENT_CONTENT_FIELD_NAME . '" rows="15" cols="150" readonly>' . $post[DB_TABLE_COMMENT_CONTENT] . '</textarea><br>
					</div>

					<div class="comment-right">
						<input type="text" id="' . COMMENT_AUTHOR_FIELD_NAME . '" name="' . COMMENT_AUTHOR_FIELD_NAME . '" value="' . $post[DB_TABLE_COMMENT_AUTHOR] . '" readonly><br>
					</div>

					</form>';
					
					print($comment);
				}
			}
			else
			{
				foreach ($result as $post) 
				{
					$comment = '<form class="master comment" method="post">
					<input class="hidden" type="text" id="' . COMMENT_ID_FIELD_NAME . '-' . $post[DB_TABLE_COMMENT_ID] . '" name="' . COMMENT_ID_FIELD_NAME . '" value="' . $post[DB_TABLE_COMMENT_ID] . '" readonly><br>
					<input class="hidden" type="text" id="' . COMMENT_POST_ID_FIELD_NAME . '-' . $post[DB_TABLE_COMMENT_POST_ID] . '" name="' . COMMENT_POST_ID_FIELD_NAME . '" value="' . $post[DB_TABLE_COMMENT_POST_ID] . '" readonly><br>
					
					<div class="comment-left">
						<textarea id="' . COMMENT_CONTENT_FIELD_NAME . '" name="' . COMMENT_CONTENT_FIELD_NAME . '" rows="15" cols="150" readonly>' . $post[DB_TABLE_COMMENT_CONTENT] . '</textarea><br>
					</div>
					
					<div class="comment-right">
						<input type="text" id="' . COMMENT_AUTHOR_FIELD_NAME . '" name="' . COMMENT_AUTHOR_FIELD_NAME . '" value="' . $post[DB_TABLE_COMMENT_AUTHOR] . '" readonly><br>
					
						<button type="submit" formaction="' . COMMENT_ACTION_PATH . '" name="' . COMMENT_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_COMMENT_UPDATE . '">Update</button>

						<button type="submit" formaction="' . COMMENT_ACTION_PATH . '" name="' . COMMENT_SUBMIT_BUTTON_NAME . '" value="' . ACTION_NAME_COMMENT_REMOVAL . '">Delete</button>
					</div>

					</form>';
													
					print($comment);
				}
			}
		}
		else
		{
			$comment = '<form class="master blog-post"></form>';

			print($comment);
		}

		return $result;
	}

	public function getPageSelector(int $from = 0) : int
	{
		//
		// TODO optimize this part, if possible.
		//
		$result = $this->readAll();
		$totalComments = count($result);

		if($totalComments <= 5)
		{
			return 0;
		}

		//
		// Get total number of pages required for storing the blog posts.
		//
		$totalPages = (int)($totalComments / 5);

		//
		// Add another page when remainder is greater than zero. Example: $totalComments = 6, $postsPerPage = 5.
		// $totalComments / $postsPerPage = 1.2 = $totalPages. $totalPages is equal to 2 pages.
		//
		if($totalComments % 5 > 0)
		{
			$totalPages++;
		}

		print('<ol class="master" id="page-selector">');

		//
		// Page selector itself.
		//
		for($page = 1; $page <= $totalPages; $page++)
		{
			print('<a href="index.php?post=' . $result[DB_TABLE_COMMENT_POST_ID] . '&from=' . $from + (($page - 1) * 5) . '">' .  $page . '</a>');
		}

		print('</ol>');

		return $totalPages;
	}
}

?>
