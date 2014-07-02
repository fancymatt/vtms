<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	if($_POST['submit_new_user']) {		
		$required_fields = array("new_user_name", "new_password", "new_privilege");
		validate_presences($required_fields);
		if(empty($errors)) {
			$user = new User();
			
			$user->user_name = $username = $db->escape_value($_POST['new_user_name']);
			$user->user_password = $db->password_encrypt($_POST['new_password']);
			$user->privilege_type = $db->escape_value($_POST['new_privilege']);
			$user->team_member_id = $db->escape_value($_POST['new_team_member']);
			$user->time_zone = $db->escape_value($_POST['new_time_zone']);
		
			$user->create();
			$_SESSION['message'] = "New user '{$username}' has been created.";
			redirect_to("admin-users.php");
		}
	}
	$all_time_zones = DateTimeZone::listIdentifiers();
?>

<?php include_layout_template('header.php'); ?>

	<div id="breadcrumbs" class="row">
		<ul class="breadcrumbs">
			<li><a href="admin-users.php">User Admin</a></li>
			<li class="current"><a href="new-user.php">New User<?php echo $user->user_name; ?></a></li>
		</ul>
	</div>

	<div id="page-header" class="row">
		<div class="small-12 columns">
			<h3>New User</h3>
		</div>
	</div>
	
	<?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	<div id="new-user-form" class="row">
		<div class="small-6 columns">
		<form action="new-user.php" method="POST">
			<label for="new_user_name">Username:</label> <input type="text" size="20" name="new_user_name" value="">
			<label for="new_password">Password:</label> <input type="password" size="20" name="new_password">
			<label for="new_privilege">Privilege Set:</label> <select name="new_privilege">
			<?php
				$privilege_types = PrivilegeType::find_all();
				foreach ($privilege_types as $privilege_type) {
					echo "<option value='";
					echo $privilege_type->id;
					echo "'>";
					echo $privilege_type->privilege;
					echo "</option>";
				} ?>
			</select>
			<label for="new_team_member">Associated Team Member:</label> <select name="new_team_member">
			<?php
				$team_members = Member::find_all_members();
				echo "<option value='5'>None</option>";
				foreach ($team_members as $team_member) {
					echo "<option value='";
					echo $team_member->id;
					echo "'>";
					echo $team_member->first_name;
					echo "</option>";
				} ?>
			</select>
			<label for="new_time_zone">Time Zone:</label>
			<select name="new_time_zone">
				<option value="">Select</option>
				<?php foreach($all_time_zones as $time_zone) {
					echo "<option value='{$time_zone}'";
					if ($time_zone == $current_user->time_zone) { 
						echo "selected";
					}
					echo ">{$time_zone}</option>";
				} ?>
				</select>
			<input type="submit" name="submit_new_user" id="submit_new_user">
		</form>
	</div>
<?php include_layout_template('footer.php'); ?>