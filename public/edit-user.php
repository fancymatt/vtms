<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$user_id=$_GET['user'];
	$user = User::find_by_id($user_id);

	if($_POST['edit_user']) {
		$edited_user_id = $db->escape_value($_POST['user_id']);
		$edited_user = User::find_by_id($user_id);
		$edited_user->user_name = $db->escape_value($_POST['user_name']);
		$new_user_password = $db->password_encrypt($_POST['password']);
		$edited_user->privilege_type = mysql_prep($_POST['privilege']);
		$edited_user->team_member_id =  mysql_prep($_POST['team_member']);
		
		$required_fields = array("user_name", "password", "privilege");
		validate_presences($required_fields);
		
		if(!empty($errors)) {
			$errors = errors();
		} else {
			$edited_user->update();
			redirect_to("admin-users.php");
		}
	}
?>

<?php include_layout_template('header.php'); ?>
	<div>
		<?php $errors = $session->errors(); ?>
		<?php echo $session->form_errors($errors); ?>
		<h2>Edit User: <?php echo $user->userName; ?></h2>
		<form action="edit-user.php?user=<?php echo $user_id;?>" method="POST">
			<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
			<p><label for="user_name">Username:</label> <input type="text" size="20" name="user_name" value="<?php echo $user->user_name; ?>"></p>
			<p><label for="password">Password:</label> <input type="password" size="20" name="password"></p>
			<p><label for="privilege">Privilege Set:</label> <select name="privilege">
			<?php
				$privilege_types = PrivilegeType::find_all();
				foreach ($privilege_types as $privilege_type) {
					echo "<option value='";
					echo $privilege_type->id;
					echo "'";
					if ($user->privilege_type == $privilege_type->id) {echo " selected"; }
					echo ">";
					echo $privilege_type->privilege;
					echo "</option>";
				} ?>
			</select></p>
			<p><label for="team_member">Associated Team Member:</label> <select name="team_member">
			<?php
				$team_members = Member::find_all_members();
				foreach ($team_members as $team_member) {
					echo "<option value='";
					echo $team_member->id;
					echo "'";
					if ($user->team_member_id == $team_member->id) {echo " selected"; }
					echo ">";
					echo $team_member->first_name;
					echo "</option>";
				} ?>
			</select></p>
			<p><input type="submit" name="edit_user" id="edit_user"></p>
		</form>
	</div>
<?php include_layout_template('footer.php'); ?>