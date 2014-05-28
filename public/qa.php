<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	if($_POST['changed_qa_log']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$qa_log = $db->escape_value($_POST['qa_log']);
		$message = "QA Log changed to: " . $qa_log;
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->qa_log = $qa_log;
		$lesson->update();
	}
	
	$qa_lessons = Lesson::find_qa_lessons();
?>

<?php include_layout_template('header.php'); ?>
		<div id="qa_lessons">
		<h2 id="main_title">QA Lessons</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Lesson</th><th>QA Status</th><th>Actions</th><th>Publish Date</th></tr>
				<?php 
				if(!$qa_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($qa_lessons as $qa_lesson) {
						echo "<tr>";
						echo "<td>";
						echo $qa_lesson->display_full_lesson();
						echo "</td>";
						echo "<td><form action='qa.php' method='post'><input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'>";
						echo "<label for='qa_log'>Log:</label><input type='text' size=40 name='qa_log' value='{$qa_lesson->qa_log}'>";
						echo "<input type='submit' name='changed_qa_log' value='Update'></form><br />";
						echo "<label for='qa_url'>Link:</label><input type='text' size=40 name='qa_url' value='{$qa_lesson->qa_url}'>";
						echo "</td>";
						echo "<td>";
						echo $qa_lesson->display_list_of_issues_with_link(); 
						echo "</td>";
						echo "<td>";
						echo $qa_lesson->publish_date;
						echo "</td>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
		</div>
<?php include_layout_template('footer.php'); ?>