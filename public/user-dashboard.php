<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$current_user_id = $_SESSION['user_id'];
	$current_user = User::find_by_id($current_user_id);
	$all_time_zones = DateTimeZone::listIdentifiers();
	
	if($_POST['user_edited']) {
		$pw1 = $db->escape_value($_POST['user_password']);
		$pw2 = $db->escape_value($_POST['user_password_confirm']);
		if($pw1 == $pw2) {
			$password = $db->password_encrypt($pw2);
			$current_user->user_password = $password;
			$current_user->update();
			$message = "Okay, your password was updated";
		} else { // Passwords don't match
			$message = "Passwords do not match";
		}
	}
?>

<?php include_layout_template('header.php'); ?>
<h2>User Dashboard</h2>
<form action="user-dashboard.php" method="post">
<?php if($message) echo "<p>".$message."</p>"; ?>
<p>User Name: <input name="user_name" value="<?php echo $current_user->user_name; ?>"></p>
<p>Password: <input name="user_password" type="password"></p>
<p>Password: (enter again) <input name="user_password_confirm" type="password"></p>
<p>Time Zone: <select name="user_time_zone">
	<?php foreach($all_time_zones as $time_zone) {
		echo "<option value='{$time_zone}'";
		if ($time_zone == $current_user->time_zone) { 
			echo "selected";
		}
		echo ">{$time_zone}</option>";
	} ?>
</select></p>
<p><input type="submit" name="user_edited" value="Edit User Information"></p>
</form>
<p>Password: <?php echo $current_user->user_password; ?></p>
<?php include_layout_template('footer.php'); ?>