<?php require_once("../includes/initialize.php"); ?>
<?php
	
	if($_POST['submit_new_user']) {		
		$required_fields = array("user_name", "password", "privilege");
		validate_presences($required_fields);
		
		if(!empty($errors)) {
			$errors = errors();
		} else {
			$user = new User();
			
			$user->user_name = $db->escape_value($_POST['user_name']);
			$user->user_password = $db->escape_value($_POST['password']);
			$user->privilege_type = $db->escape_value($_POST['privilege']);
			if($$_POST['team_member']) { $user->team_member_id = $db->escape_value($_POST['team_member']); }
			$user->time_zone = $db->escape_value($_POST['time_zone']);
		
			$user->create();
			redirect_to("admin-users.php");
		}
	}
?>

<?php include_layout_template('header.php'); ?>
	<div>
		<?php $errors = $session->errors(); ?>
		<?php echo $session->form_errors($errors); ?>
		<h2>New User</h2>
		<form action="new-user.php" method="POST">
			<p><label for="user_name">Username:</label> <input type="text" size="20" name="user_name" value=""></p>
			<p><label for="password">Password:</label> <input type="password" size="20" name="password"></p>
			<p><label for="privilege">Privilege Set:</label> <select name="privilege">
			<?php
				$privilege_types = PrivilegeType::find_all();
				foreach ($privilege_types as $privilege_type) {
					echo "<option value='";
					echo $privilege_type->id;
					echo "'>";
					echo $privilege_type->privilege;
					echo "</option>";
				} ?>
			</select></p>
			<p><label for="team_member">Associated Team Member:</label> <select name="team_member">
			<?php
				$team_members = Member::find_all_members();
				echo "<option value='0'>--</option>";
				foreach ($team_members as $team_member) {
					echo "<option value='";
					echo $team_member->id;
					echo "'>";
					echo $team_member->first_name;
					echo "</option>";
				} ?>
			</select></p>
			<p><label for="time_zone">Time Zone: </label> <input type="text" size="20" name="time_zone" value=""></p>
			<p><input type="submit" name="submit_new_user" id="submit_new_user"></p>
		</form>
	</div>
<?php include_layout_template('footer.php'); ?>