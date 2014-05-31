<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$users = User::find_all();
?>

<?php include_layout_template('header.php'); ?>
		<div>
		<?php echo $session->message(); ?>
		<h2>Manage Users</h2>
		<p>Warning, this functionality is not currently working. Please talk to Matt for user administration.</p>
		<table>
			<tr><th>User ID</th><th>Privilege Type</th><th>Team Member</th><th>Edit</th><th>Delete</th></tr>
			<?php foreach($users as $user) {
				echo "<tr>";
				echo "<td>{$user->user_name}</td>";
				echo "<td>{$user->privilege_type}</td>";
				echo "<td>{$user->member_name}</td>";
				echo "<td><a href='edit-user.php?user={$user->id}'>Edit</a></td>"; ?>
				<td><a href="delete-user.php?user=<?php echo $user->id; ?>" onclick="return confirm('Are you sure?');">Delete</a></td></tr>
				<?php
				} ?>
			<tr><td><a href="new-user.php">Add New User</a></td><td></td><td></td></tr>
			</table>
		</div>
<?php include_layout_template('footer.php'); ?>