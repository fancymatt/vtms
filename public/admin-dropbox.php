<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	if($_POST['add_to_dropbox']) {
		$dropbox_lesson_id = $db->escape_value($_POST['dropbox_lesson_id']);
		$lesson = Lesson::find_by_id($dropbox_lesson_id);
		$lesson->add_to_dropbox();
	}

	$dropbox_tasks = Task::get_tasks_waiting_on_dropbox();
?>

<?php include_layout_template('header.php'); ?>
		<h2 class="main_title">Dropbox</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
	<div id="dropbox-lessons">
		<h3>Dropbox Lessons</h3>	
		<p><a href="operations.php"><- Return to Operations page</a></p>
		<table>
			<tr><th>Lesson</th><th>Task</th><th>Member</th><th>Actions</th></tr>
				<?php 
				if(!$dropbox_tasks) {
					echo "<td>No lessons</td>";
				} else {
					foreach($dropbox_tasks as $task) {
						echo "<tr>";
						echo "<td>";
						echo $task->display_full_task_lesson();
						echo "</td>";
						echo "<td>{$task->task_name}</td>";
						echo "<td>{$task->team_member_name}</td>";
						echo "<td><form action='admin-dropbox.php' method='post'>";
						echo "<input type='hidden' name='dropbox_lesson_id' value='{$task->lesson_id}'><input type='submit' name='add_to_dropbox' value='Add to Dropbox'></form></td>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
	</div>
		
<?php include_layout_template('footer.php'); ?>