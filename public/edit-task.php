<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	$task_id = $db->escape_value($_GET['id']);
	$task = Task::find_by_id($task_id);
	$logged_in_user = User::find_by_id($session->user_id);
		
	if($_POST['edited_task']) {
		$task->is_completed = $db->escape_value($_POST['is_completed']);
		if($task->is_asset) {
			$task->is_delivered = $db->escape_value($_POST['is_delivered']);
		} else {
			$time_hours = $db->escape_value($_POST['time_actual_hours']);
			$time_minutes = $db->escape_value($_POST['time_actual_minutes']);
			$time_seconds = $db->escape_value($_POST['time_actual_seconds']);
			$time_actual = ($time_hours*3600)+($time_minutes*60)+($time_seconds);
			$task->time_actual = $time_actual;
		}
		$task->team_member_id = $db->escape_value($_POST['team_member_id']);
		$task->update();
		redirect_to("lesson.php?series={$task->series_id}&langSeries={$task->language_series_id}&lesson={$task->lesson_id}");
	}
	$time_actual = (int) $task->time_actual;
	$time_actual_seconds = (int) ($time_actual%60);
	($time_actual < 3600) ? $time_actual_minutes = (int) ($time_actual/60) : $time_actual_minutes = (int) ($time_actual%3600);
	
	$time_actual_hours = (int) ($time_actual/3600);
	
	$team_members = Member::find_all_members();
	$lesson = Lesson::find_by_id($task->lesson_id);
?>

<?php include_layout_template('header.php'); ?>


		<div class="row">
		<h2><?php echo $task->display_full_task_lesson(). " - " .$task->task_name; ?>
		</h2>
		<?php echo $session->message(); ?>
		<p><a href="lesson.php?series=<?php echo $lesson->series_id; ?>&langSeries=<?php echo $lesson->language_series_id; ?>&lesson=<?php echo $task->lesson_id; ?>"><- Return to Lesson Page</a></p>
		
		<p>Original Due Date: <?php echo $task->task_due_date; ?></p>
		
		<form action="edit-task.php?id=<?php echo $task->id; ?>" method="post">
			<p><label for="is_completed">Completed?</label><input type="checkbox" name="is_completed" value="1" <?php echo $task->is_completed ? "checked" : ""; ?>></p>
			<?php if ($task->is_completed) {
				echo "<p>Time Completed: ".$logged_in_user->local_time($task->completed_time);
			} ?>
			<?php if ($task->is_asset) { ?>
				<p><label for="is_delivered">Delivered?</label><input type="checkbox" name="is_delivered" value="1" <?php echo $task->is_delivered ? "checked" : ""; ?>></p>
			<?php } else { ?>
				<p><label for="time_actual_hours">TRT:</label> 
				<select name="time_actual_hours" id="time_actual_hours">
					<?php for($i=0; $i<10; $i++) {
						echo "<option value='{$i}'";
						if ($i == $time_actual_hours) {
							echo " selected";
						}
						echo ">{$i}</option>";
					} ?>
				</select>:
				<select name="time_actual_minutes" id="time_actual_minutes">
					<?php for($i=0; $i<60; $i++) {
						echo "<option value='{$i}'";
						if ($i == $time_actual_minutes) {
							echo " selected";
						}
						echo ">{$i}</option>";
					} ?>
				</select>:
				<select name="time_actual_seconds" id="time_actual_seconds">
					<?php for($i=0; $i<60; $i++) {
						echo "<option value='{$i}'";
						if ($i == $time_actual_seconds) {
							echo " selected";
						}
						echo ">{$i}</option>";
					} ?>
				</select>
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
	