<?php require_once("../includes/initialize.php"); ?>
<?php
	if($_POST['changed_qa_log']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$qa_log = $db->escape_value($_POST['qa_log']);
		$message = "QA Log changed to: " . $qa_log;
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->qa_log = $qa_log;
		$lesson->update();
	}
	
	if($_POST['changed_qa_url']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$qa_url = $db->escape_value($_POST['qa_url']);
		$message = "QA URL changed to: " . $qa_url;
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->qa_url = $qa_url;
		$lesson->update();
	}
	
	$qa_lessons = Lesson::find_all_qa_lessons();
?>

<?php include_layout_template('header.php'); ?>
		<div id="qa_lessons">
		<h2 id="main_title">QA Lessons</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Lesson</th><th>QA Status</th><th>Actions</th><th>Due Date</th></tr>
				<?php 
				if(!$qa_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($qa_lessons as $qa_lesson) {
						echo "<tr>";
						echo "<td>{$qa_lesson->language_name} {$qa_lesson->series_name} #{$qa_lesson->number} ({$qa_lesson->level_code})";
						echo "<td><form action='admin_qa.php' method='post'>";
						echo "<input type='text' size=70 name='qa_log' value='{$qa_lesson->qa_log}'>";
						echo "<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'><input type='submit' name='changed_qa_log'></form>";
						echo "<form action='admin_qa.php' method='post'>";
						echo "<input type='text' size=70 name='qa_url' value='{$qa_lesson->qa_url}'>";
						echo "<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'><input type='submit' name='changed_qa_url'></form></td>";
						echo "<td><a href='reportIssuesForQALesson.php?id=" . $qa_lesson->id . "'>Add Issue</a>";
						echo "</td>";
						echo "<td>{$qa_lesson->date_due}</td>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
		</div>
<?php include_layout_template('footer.php'); ?>