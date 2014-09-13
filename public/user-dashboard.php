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

<?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	

<div class="small-12 medium-8 medium-centered columns">
  <h3>User Dashboard</h3>
  <div class="panel">
    <h4>User Info</h4>
    <div class="small-12 medium-3 columns">
      <label>User Name: 
    </div>
    <div class="small-12 medium-9 columns">
      <form action="user-dashboard.php" method="post">
      <input name="user_name" value="<?php echo $current_user->user_name; ?>"></label>
    </div>
  
  
  
  <label>Time Zone: <select name="user_time_zone">
  	<?php foreach($all_time_zones as $time_zone) {
  		echo "<option value='{$time_zone}'";
  		if ($time_zone == $current_user->time_zone) { 
  			echo "selected";
  		}
  		echo ">{$time_zone}</option>";
  	} ?>
  </select></label>
  </div>
  <div class="panel">
  <h4>Change Password</h4>
  <label>New Password: <input name="user_password" type="password"></label>
  <label>New Password: <input name="user_password_confirm" type="password" placeholder="please re-enter"></label>
  </div>
  <div class="panel">
    <label>Current Password: <input name="user_old_password" type="password" placeholder="required to edit"></label>
    <input type="submit" name="user_edited" class="action button" value="Edit User Information">
    </form>
  </div>
</div>
<?php include_layout_template('footer.php'); ?>