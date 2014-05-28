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
	
	$exportable_lessons = count(Lesson::find_all_exportable_lessons());
	$queued_lessons = count(Lesson::find_all_queued_lessons());
	
	$moveable_lessons = count(Lesson::find_all_moveable_lessons());
	$language_checked_lessons = count(Lesson::find_all_ready_to_video_check_lessons());
	
	$qa_lessons = Lesson::find_all_checkable_lessons();
?>

<?php include_layout_template('header.php'); ?>
		<h2 class="main_title">Operations</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
	<div id="export-queue" class="panel">
		<p>Ready to Render: <strong><?php echo $exportable_lessons; ?></strong></p>
		<p>Currently in Queue: <strong><?php echo $queued_lessons; ?></strong></p>
		<p><a href="export.php">Go to Render Page</a></p>
	</div>
	<div id="video-checking" class="panel">
		<p>Files to Archive: <strong><?php echo $moveable_lessons; ?></strong></p>
		<p>Ready to Check: <strong><?php echo $language_checked_lessons; ?></strong></p>
		<p><a href="videoCheck.php">Go to Video Check Page</a></p>
	</div>
	<div id="get-these-checked">
	<h3>Waiting for Language Check</h3>
		<table>
			<tr><th>Lesson</th><th>QA Status</th><th>Actions</th><th>Exported Time</th></tr>
				<?php 
				if(!$qa_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($qa_lessons as $qa_lesson) {
						$issues = Issue::get_unfinished_issues_for_lesson($qa_lesson->id);
						
						echo "<tr>";
						echo "<td>";
						echo $qa_lesson->display_full_lesson();
						echo "</td>";
						echo "<td><form action='operations.php' method='post'>";
						echo "<input type='text' size=40 name='qa_log' value='{$qa_lesson->qa_log}'><br />";
						echo "<input type='text' size=40 name='qa_url' value='{$qa_lesson->qa_url}'><br />";
						echo "<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'><input type='submit' name='changed_qa'></form></td>";
						echo "<td><form action='operations.php' method='post'>";
						echo "Issues: ".count($issues);
						echo "<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'><input type='submit' name='marked_lesson_language_checked' value='Mark Language Checked'></form></td>";
						echo "<td>{$qa_lesson->exported_time}</td>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
	</div>
		
<?php include_layout_template('footer.php'); ?>