<?php require_once("../includes/initialize.php"); ?>
<?php
	if($_POST['mark_lesson_as_checked']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->checked_video = 1;
		$lesson->update();
	}
	
	if($_POST['move_lesson']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->files_moved = 1;
		$lesson->update();
	}
	
	$moveable_lessons = Lesson::find_all_moveable_lessons();
	$language_checked_lessons = Lesson::find_all_ready_to_video_check_lessons();
?>

<?php include_layout_template('header.php'); ?>
		<h2 id="main_title">Video Checking</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<p><a href="operations.php"><- Return to Operations page</a></p>
		<div id="move-these">
		<h3>Move These</h3>
		<p>Completed checked but not moved</p>
		<table>
			<tr><th>Lesson</th><th>Actions</th><th>Due Date</th></tr>
				<?php 
				if(!$moveable_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($moveable_lessons as $qa_lesson) {
						echo "<tr>";
						echo "<td>";
						echo $qa_lesson->display_full_lesson();
						echo "</td>";
						echo "<td><form action='videoCheck.php' method='post'>";
						echo "<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'><input type='submit' name='move_lesson' value='Files Moved'></form>";
						echo "</td>";
						echo "<td>{$qa_lesson->date_due}</td>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
	</div>
	<br />
	<div id="check-these">
		<h3>Check These</h3>
		<table>
			<tr><th>Lesson</th><th>Actions</th><th>Due Date</th></tr>
				<?php 
				if(!$language_checked_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($language_checked_lessons as $qa_lesson) {
						echo "<tr>";
						echo "<td>";
						echo $qa_lesson->display_full_lesson();
						echo "</td>";
						echo "<td><a href='reportIssuesForQALesson.php?id=" . $qa_lesson->id . "'>Add Issue</a> | ";
						echo "<form action='videoCheck.php' method='post'>";
						echo "<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'><input type='submit' name='mark_lesson_as_checked' value='Mark as Checked'></form>";
						echo "</td>";
						echo "<td>{$qa_lesson->date_due}</td>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
	</div>
	<p><a href="operations.php"><- Return to Operations page</a></p>
		
<?php include_layout_template('footer.php'); ?>