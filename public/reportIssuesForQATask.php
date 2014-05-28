<?php require_once("../includes/initialize.php"); ?>
<?php
	$session->confirm_logged_in();
	
	$current_task_id = $_GET['id'];
	$current_task = Task::find_by_id($current_task_id);
	
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
			redirect_to("reportIssuesForQALesson.php?id={$current_task->lesson_id}");
		}
				
	} 

	if (!$current_task->id) {
		redirect_to("reportIssuesForQALesson.php?id={$current_task->lesson_id}");
	}	
?>

<?php include_layout_template('header.php'); ?>
	<div>
		<p><a href="reportIssuesForQALesson.php?id=<?php echo $current_task->lesson_id; ?>"><- Return to Lesson</a></p>
		<h2>Add an issue: <?php echo $current_lesson->title; ?></h2>
		<form action='reportIssuesForQATask.php?id=<?php echo $current_task_id; ?>' method='post'>
		<p><label for="timecode">Timecode: </label><input type="text" placeholder="ex: 2:25" name="timecode"></p>
		<p><label for="creator">Creator: </label><input type="text" value="Checker" name="creator"></p>
		<p><label for="body">Issue: </label><input type="text" placeholder="ex: Change 'das' to 'der'" name="body" size="120"></p>
		<input type="hidden" name="submitted_issue_task_id" value="<?php echo $current_task->id; ?>">
		<p><input type="submit" name="submitted_issue" value="Report and Go Back">
		<input type="submit" name="submitted_issue_and_repeat" value="Report and Continue"></p>
		<p><a href="reportIssuesForQALesson.php?id=<?php echo $current_task->lesson_id; ?>"><- Return to Lesson</a></p>
		<br />
	</div>
		
		
<?php include_layout_template('footer.php'); ?>