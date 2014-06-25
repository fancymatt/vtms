<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
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

	<div id="breadcrumbs" class="row">
		<ul class="breadcrumbs">
			<li><a href="admin-users.php">User Admin</a></li>
			<li class="current"><a href="edit-user.php?user=<?php echo $user_id; ?>">Edit: <?php echo $user->user_name; ?></a></li>
		</ul>
	</div>

	<div id="page-header" class="row">
		<div class="small-12 columns">
			<h3>Edit User</h3>
		</div>
	</div>
	
	<?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	<div id="user-list" class="row">
		<div class="small-6 columns">
		<form action="edit-user.php?user=<?php echo $user_id;?>" method="POST">
			<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
			<label for="user_name">Username:</label> <input type="text" size="20" name="user_name" value="<?php echo $user->user_name; ?>">
			<label for="password">Password:</label> <input type="password" size="20" name="password">
			<label for="privilege">Privilege Set:</label> <select name="privilege">
			<?php
				$privilege_types = PrivilegeType::find_all();
				foreach ($privilege_types as $privilege_type) {
					echo "<option value='";
					echo $privilege_type->id;
					echo "'";
					if ($user->privilege_type == $privilege_type->id) { echo " selected"; }
					echo ">";
					echo $privilege_type->privilege;
					echo "</option>";
				} ?>
			</select>
			<label for="team_member">Associated Team Member:</label> <select name="team_member">
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
			</select>
			<input type="submit" name="edit_user" id="edit_user" class="button">
		</form>
		</div>
	</div>
<?php include_layout_template('footer.php'); ?>