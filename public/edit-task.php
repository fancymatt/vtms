<?php require_once("../includes/initialize.php"); ?>
<?php
	$task_id = $db->escape_value($_GET['id']);
	$task = Task::find_by_id($task_id);
		
	if($_POST['edited_task']) {
		$task->is_completed = $db->escape_value($_POST['is_completed']);
		if($task->is_asset) {
			$task->is_delivered = $db->escape_value($_POST['is_delivered']);
		} else {
			$task->time_actual = $db->escape_value($_POST['time_actual']);
		}
		$task->team_member_id = $db->escape_value($_POST['team_member_id']);
		$task->update();
		redirect_to("lesson.php?series={$task->series_id}&langSeries={$task->language_series_id}&lesson={$task->lesson_id}");
	}

	$team_members = Member::find_all_members();
	$lesson = Lesson::find_by_id($task->lesson_id);
?>

<?php include_layout_template('header.php'); ?>
		<div>
		<h2><?php echo $task->display_full_task_lesson(). " - " .$task->task_name; ?>
		</h2>
		<?php echo $session->message(); ?>
		<p><a href="lesson.php?series=<?php echo $lesson->series_id; ?>&langSeries=<?php echo $lesson->language_series_id; ?>&lesson=<?php echo $task->lesson_id; ?>"><- Return to Lesson Page</a></p>
		<form action="edit-task.php?id=<?php echo $task->id; ?>" method="post">
			<p><label for="is_completed">Completed?</label><input type="checkbox" name="is_completed" value="1" <?php echo $task->is_completed ? "checked" : ""; ?>></p>
			<?php if ($task->is_asset) { ?>
				<p><label for="is_delivered">Delivered?</label><input type="checkbox" name="is_delivered" value="1" <?php echo $task->is_delivered ? "checked" : ""; ?>></p>
			<?php } else { ?>
				<p><label for="time_actual">Time Actual</label><input type="text" name="time_actual" value="<?php echo $task->time_actual; ?>"></p>
			<?php } ?>
			<p><label for="team_member_id">Assigned Team Member:</label> <select name="team_member_id">
			<?php
				foreach ($team_members as $team_member) {
					echo "<option value='";
					echo $team_member->id;
					echo "'";
					if ($task->team_member_id == $team_member->id) {echo " selected"; }
					echo ">";
					echo $team_member->first_name;
					echo "</option>";
				} ?>
			</select></p>
			<p><input type="submit" value="Edit" name="edited_task"></p>
		</form>
		<p><a href="lesson.php?series=<?php echo $lesson->series_id; ?>&langSeries=<?php echo $lesson->language_series_id; ?>&lesson=<?php echo $task->lesson_id; ?>"><- Return to Lesson Page</a></p>
		</div>
<?php include_layout_template('footer.php'); ?>
	