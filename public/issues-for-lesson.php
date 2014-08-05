<?php require_once("../includes/initialize.php"); ?>
<?php
	$session->confirm_logged_in();
	
	if($_POST['changed_qa_log']) {
		$qa_lesson_id = $_POST['qa_lesson_id'];
		$qa_log = $_POST['qa_log'];
		$message = "QA Log changed to: " . $qa_log;
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->qa_log = $qa_log;
		$lesson->update();
	}
	
	$current_lesson_id = $db->escape_value($_GET['id']);
	$current_lesson = Lesson::find_by_id($current_lesson_id);
	$tasks = Task::find_all_assets_and_tasks_for_lesson($current_lesson->id);
	$issues = Issue::get_all_issues_for_lesson($current_lesson->id);
	if (!$current_lesson->title) {
		redirect_to("qa.php");
	}

?>

<?php include_layout_template('header.php'); ?>
	<div class="row">
		<a href="qa.php" class="button">QA Lessons</a>    <a href="lesson.php?id=<?php echo $current_lesson->id; ?>" class="button">Return to Lesson</a>
		<h2><?php echo $current_lesson->display_full_lesson().": ".$current_lesson->title; ?></h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<div id="issues-list">
		<table>
			<tr><th>Task</th><th>Creator</th><th>Timecode</th><th>Issue</th><th>Status</th>
			<?php foreach($issues as $issue): ?>
			<?php $task_for_issue_id = Task::find_by_id($issue->task_id); ?>
			<tr>
				<td><?php echo $task_for_issue_id->task_name; ?></td>
				<td><?php echo $issue->issue_creator; ?></td>
				<td><?php echo $issue->issue_timecode; ?></td>
				<td><?php echo $issue->issue_body; ?></td>
				<td><?php echo $issue->is_completed ? "Finished" : "Incomplete"; ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
		<p>Current QA Log: <form action='issues-for-lesson.php?id=<?php echo $current_lesson->id; ?>' method='post'><input type='text' size=70 name='qa_log' value='<?php echo $current_lesson->qa_log; ?>'>
					<input type='hidden' name='qa_lesson_id' value='<?php echo $current_lesson->id; ?>'>
					<input type='submit' name='changed_qa_log'>
					</form></p>
		<h3>What's the problem?</h3>
		<ul>
		<?php foreach($tasks as $task) {
			$global_task = GlobalTask::find_by_id($task->global_task_id);
			if ($global_task->can_add_issues) { ?>
				<li><a href="issues-for-task.php?id=<?php echo $task->id; ?>"><?php echo $global_task->issue_reporting_friendly_text; ?></a></li>
			<?php }
		} ?>
		</ul>
		<a href="qa.php" class="button">QA Lessons</a>    <a href="lesson.php?id=<?php echo $current_lesson->id; ?>" class="button">Return to Lesson</a>
	</div>
		
		
<?php include_layout_template('footer.php'); ?>