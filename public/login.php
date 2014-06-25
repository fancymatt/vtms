<?php require_once("../includes/initialize.php"); ?>
<?php
	if($_POST['login_user']) {
		$required_fields = array("username", "password");
		validate_presences($required_fields);
		
		$submitted_username = $_POST['username'];
		$submitted_password = $_POST['password'];
		$message = "";
		
		if(!empty($session->errors)) {
			// there are errors
			
		} else {
			// attempt login
			$found_user = User::attempt_login($submitted_username, $submitted_password);
			
			if ($found_user) {
				$session->login($found_user);
				if($found_user->team_member_id == 5) {
					redirect_to("qa.php");
				} else {
					redirect_to("task-sheet.php?member=".$found_user->team_member_id);
				}
			} else {
				$message .= "Username or password was wrong.";
			}
		}
	}
?>
<?php include_layout_template('header.php'); ?>
	<div id="page-header" class="row">
		<div class="small-12 columns">
			<h3>Log In</h3>
		</div>
	</div>
	
	<?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	
	<div id="log-in" class="panel">
		<form method="POST" action="login.php">
			<p><label for="username">Username:</label><input type="text" name="username" value="<?php echo $submitted_username; ?>"></p>
			<p><label for="password">Password:</label><input type="password" name="password"></p>
			<p><input type="submit" id="login_user" name="login_user"></p>
		</form>
	</div>
	
<?php include_layout_template('footer.php'); ?>