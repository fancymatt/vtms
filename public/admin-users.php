<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$users = User::find_all();

	if($_POST['deleted_user']) {
		$user = User::find_by_id($db->escape_value($_POST['deleted_user_id']));
		if (!$user) {
			redirect_to("admin-users.php");
		}
		
		$user->delete();
		$_SESSION['message'] = "User '{$user->user_name}' has been deleted.";
		redirect_to("admin-users.php");
	}
?>

<?php include_layout_template('header.php'); ?>
	
	<div id="breadcrumbs" class="row">
		<ul class="breadcrumbs">
			<li class="current"><a href="admin-users.php">User Admin</a></li>
		</ul>
	</div>
	
	<div id="page-header" class="row">
		<div class="small-12 columns">
			<h3>Manage Users</h3>
		</div>
	</div>
	
	<?php if($message) { ?>
		<div data-alert class="alert-box">
		  <?php echo $message; ?>
		  <a href="#" class="close">&times;</a>
		</div>
	<?php } ?>
	<div id="ready-to-upload" class="row">
		<div class="small-6 columns">
		<table>
			<tr><th>User ID</th><th>Privilege Type</th><th>Team Member</th><th>Edit</th><th>Delete</th></tr>
			<?php foreach($users as $user) {
				echo "<tr>";
				echo "<td>{$user->user_name}</td>";
				echo "<td>{$user->privilege_type}</td>";
				echo "<td>{$user->member_name}</td>";
				echo "<td><a href='edit-user.php?user={$user->id}'>Edit</a></td>"; ?>
				<td>
					<form action="admin-users.php" method="POST">
					<input type="hidden" name="deleted_user_id" id="deleted_user_id" value="<?php echo $user->id; ?>">
					<input type="submit" name="deleted_user" id="deleted_user" value="Delete User" onclick="return confirm('Are you sure you want to delete this item?')">
					</form>
				</td>
			</tr>
			<?php
			} ?>
			<tr>
				<td colspan="3"><a href="new-user.php">Add New User</a>
			</tr>
			</table>
		</div>
	</div>
<?php include_layout_template('footer.php'); ?>