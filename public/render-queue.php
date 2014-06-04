<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	$current_time = new DateTime(null, new DateTimeZone('UTC'));
	$logged_in_user = User::find_by_id($session->user_id);
	
	if(isset($_POST['add_lesson_to_queue'])) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->is_queued = 1;
		$lesson->queued_time = $current_time->format('Y-m-d H:i:s');
		$lesson->update();
	}
	
	if(isset($_POST['mark_lesson_as_exported'])) {
		// and update qa fields
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->is_queued = 0;
		$lesson->exported_time = $current_time->format('Y-m-d H:i:s');
		$lesson->update();
	}
	
	if(isset($_POST['mark_lesson_as_exported_and_updated'])) {
		// and update qa fields
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->is_queued = 0;
		$lesson->qa_log = $db->escape_value($_POST['qa_log']);
		$lesson->qa_url = $db->escape_value($_POST['qa_url']);
		$lesson->exported_time = $current_time->format('Y-m-d H:i:s');
		$lesson->update();
	}
	
	if(isset($_POST['unqueue_lesson'])) {
		// and update qa fields
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->is_queued = 0;
		$lesson->exported_time = '';
		$lesson->update();
	}
		
	$exportable_lessons = Lesson::find_all_exportable_lessons();
	$queued_lessons = Lesson::find_all_queued_lessons();
?>

<?php include_layout_template('header.php'); ?>
		<h2 id="main_title">Exporting</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
	<p><a href="operations.php"><- Return to Operations page</a></p>
	<div id="export-these">
		<h3>Export These</h3>
		<table>
			<tr><th>Lesson</th><th>Last Action</th><th>Action Time</th><th>Actions</th><th>Due Date</th></tr>
				<?php 
				if(!$exportable_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($exportable_lessons as $qa_lesson) {
						echo "<tr>";
						echo "<td>";
						echo $qa_lesson->display_full_lesson();
						echo "</td>";
						echo "<td>";
						if($qa_lesson->last_action == 'task') {
							$last_task = Task::find_by_id($qa_lesson->last_task_id);
							echo $last_task->team_member_name." - ".$last_task->task_name;
						} else {
							$last_issue = TaskComment::find_by_id($qa_lesson->last_issue_id);
							echo $last_issue->team_member_name. " - Issue Fixed";
						}
						echo "</td>";
						echo "<td>";
						if($qa_lesson->last_action == 'task') {
							echo $logged_in_user->local_time($qa_lesson->last_task_time);
						} else {
							echo $logged_in_user->local_time($qa_lesson->last_issue_time);
						}
						echo "</td>";
						echo "<td><form action='render-queue.php' method='post'>";
						echo "<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'><input type='submit' name='add_lesson_to_queue' value='Add To Queue'></form></td>";
						echo "<td>{$qa_lesson->publish_date}</td>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
	</div>
		
	<div id="export-queue">
		<h3>Export Queue</h3>
		<table>
			<tr><th>Lesson</th><th>Queued Time</th><th>Actions</th></tr>
				<?php 
				if(!$queued_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($queued_lessons as $qa_lesson) {
						echo "<tr>";
						echo "<td>";
						echo $qa_lesson->display_full_lesson();
						echo "</td>";
						echo "<td>".$logged_in_user->local_time($qa_lesson->queued_time)."</td>";
						echo "<td><form action='render-queue.php' method='post'>";
						if($qa_lesson->checked_language) {
							echo "<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'><input type='submit' name='mark_lesson_as_exported' value='Exported'></form>";
						} else {
							echo "Log: <input type='text' size=60 name='qa_log' value='{$qa_lesson->qa_log}'><br />";
							echo "URL: <input type='text' size=60 name='qa_url' value='{$qa_lesson->qa_url}'>";
							echo "<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'><input type='submit' name='mark_lesson_as_exported_and_updated' value='Exported & Updated'></form>";
						}
						echo "<form action='render-queue.php' method='post'><input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'><input type='submit' name='unqueue_lesson' value='Unqueue!'></form>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
	</div>
	<p><a href="operations.php"><- Return to Operations page</a></p>

<?php include_layout_template('footer.php'); ?>