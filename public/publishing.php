<?php require_once("../includes/initialize.php"); ?>
<?php
	if($_POST['changed_qa']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$qa_log = $db->escape_value($_POST['qa_log']);
		$qa_url = $db->escape_value($_POST['qa_url']);
		$message = "QA Log changed to: " . $qa_log;
		$message = "QA URL changed to: " . $qa_url;
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->qa_log = $qa_log;
		$lesson->qa_url = $qa_url;
		$lesson->update();
	}
	
	if($_POST['marked_lesson_language_checked']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->checked_language = 1;
		$lesson->update();
	}
	
	if($_POST['add_to_dropbox']) {
		$dropbox_lesson_id = $db->escape_value($_POST['dropbox_lesson_id']);
		$lesson = Lesson::find_by_id($dropbox_lesson_id);
		$lesson->add_to_dropbox();
	}
	
	$recently_completed_lessons = Lesson::find_all_recently_completed_lessons();
	
	$sort_by = $db->escape_value($_GET['sort']);	
	$qa_lessons = Lesson::find_all_checkable_lessons($sort_by);
?>

<?php include_layout_template('header.php'); ?>
		<h2 class="main_title">Publishing</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
	<div id="recently-finished-lessons">
	<h3>Finished, no time added</h3>
		<table>
			<tr><th>Lesson</th><th>TRT</th><th>Actions</th></tr>
				<?php 
				if(!$qa_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($recently_completed_lessons as $lesson) {
						echo "<tr>";
						echo "<td>";
						echo $lesson->display_full_lesson();
						echo "</td>";
						echo "<td>";
						echo $lesson->trt;
						echo "</td>";
						echo "<td>";
						echo "<a href='lesson.php?series={$lesson->series_id}&langSeries={$lesson->language_series_id}&lesson={$lesson->id}'>View</a>";
						echo "</td>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
	</div>
		
<?php include_layout_template('footer.php'); ?>