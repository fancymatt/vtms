<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$current_user_id = $_SESSION['user_id'];
	$current_user = User::find_by_id($current_user_id);
	$all_time_zones = DateTimeZone::listIdentifiers();
	
	if($_POST['user_edited']) {
		
		$pw1 = $db->escape_value($_POST['user_password']);
		$pw2 = $db->escape_value($_POST['user_password_confirm']);
		$old_pw = $db->escape_value($_POST['user_old_password']);
		$username = $db->escape_value($_POST['user_name']);
		$time_zone = $db->escape_value($_POST['user_time_zone']);
	
		$found_user = User::attempt_login($current_user->user_name, $old_pw);
		
		if ($found_user) {
			$current_user->user_name = $username;
			$current_user->time_zone = $time_zone;
			if ($pw1 != "") {
				// Is attempting password change
				if($pw1 == $pw2) {
					$password = $db->password_encrypt($pw2);
					$current_user->user_password = $password;
				} else { // Passwords don't match
					$message .= "Passwords do not match";
				}
			}
			$current_user->update();
			$message = "Changes saved.";
		} else {
			$message = "Please enter your current password in order to make changes.";
		}
	}
?>

<?php include_layout_template('header.php'); ?>
<h2>User Dashboard</h2>
<form action="user-dashboard.php" method="post">
<?php if($message) echo "<p>".$message."</p>"; ?>
<div class="panel">
<h3>User Info</h3>
<p>User Name: <input name="user_name" value="<?php echo $current_user->user_name; ?>"></p>
<p>Time Zone: <select name="user_time_zone">
	<?php foreach($all_time_zones as $time_zone) {
		echo "<option value='{$time_zone}'";
		if ($time_zone == $current_user->time_zone) { 
			echo "selected";
		}
		echo ">{$time_zone}</option>";
	} ?>
</select></p>
</div>
<div class="panel">
<h3>Change Password</h3>
<p>New Password: <input name="user_password" type="password"></p>
<p>New Password: <input name="user_password_confirm" type="password" placeholder="please re-enter"></p>
</div>
<p>Current Password: <input name="user_old_password" type="password" placeholder="required to edit"></p>
<p><input type="submit" name="user_edited" value="Edit User Information"></p>
</form>
<?php include_layout_template('footer.php'); ?>