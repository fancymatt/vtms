<?php require_once("../includes/initialize.php"); ?>
<?php
	$session->confirm_logged_in();
	
	$current_task_id = $_GET['id'];
	$current_task = Task::find_by_id($current_task_id);
	$current_lesson = Lesson::find_by_id($current_task->lesson_id);
	
	if($_POST['submitted_issue'] || $_POST['submitted_issue_and_repeat'] ) { 
		$issue_task_id = $db->escape_value($_POST['submitted_issue_task_id']);
		$issue_timecode = $db->escape_value($_POST['timecode']);
		$issue_creator = $db->escape_value($_POST['creator']);
		$issue_body = $db->escape_value($_POST['body']);
		
		$issue = new Issue;
		$issue->task_id = $issue_task_id;
		$issue->issue_timecode = $issue_timecode;
		$issue->issue_creator = $issue_creator;
		$issue->issue_body = $issue_body;
		$issue->create();
		if($_POST['submitted_issue']) {
			redirect_to("issues-for-lesson.php?id={$current_task->lesson_id}");
		}
				
	} 

	if (!$current_task->id) {
		redirect_to("issues-for-lesson.php?id={$current_task->lesson_id}");
	}	
?>

<?php include_layout_template('header.php'); ?>
	
	<div>
		<p><a href="issues-for-lesson.php?id=<?php echo $current_task->lesson_id; ?>"><- Return to Lesson</a></p>
		<h3>Add an issue for <?php $current_lesson->display_full_lesson(); ?></h3>
		<div class="panel">
			<h4><?php echo $current_task->task_name." by ".$current_task->team_member_name; ?></h4>
			<form action='issues-for-task.php?id=<?php echo $current_task_id; ?>' method='post'>
			<label for="timecode">Timecode: </label><input type="text" placeholder="ex: 2:25" name="timecode" size="10">
			<label for="creator">Creator: </label><input type="text" value="Checker" name="creator">
			<p><textarea name="body" rows="7" cols="52"></textarea>
			<input type="hidden" name="submitted_issue_task_id" value="<?php echo $current_task->id; ?>">
			<p><input type="submit" name="submitted_issue" value="Report and Go Back">
			<input type="submit" name="submitted_issue_and_repeat" value="Report and Continue"></p>
		</div>
		<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
	</div>	
		
<?php include_layout_template('footer.php'); ?>