<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$days_past = 1;
	$recent_issues = Issue::get_recently_completed_issues($days_past);
	$actionable_issues = Issue::get_all_unfinished_issues();
	$logged_in_user = User::find_by_id($session->user_id);
?>

<?php include_layout_template('header.php'); ?>
		
		<div id="recent_issues">
		<h2>Issues Completed in the last <?php if ($days_past == 1) { echo "day"; } else { echo $days_past . " days"; } ?></h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Member</th><th>Lesson</th><th>Issue</th><th>Completed Time</th></tr>
			<?php 
				if ($recent_issues) {
					foreach($recent_issues as $issue) {
						$task = Task::find_by_id($issue->task_id);
						echo "<tr>";
						echo "<td>{$issue->team_member_name}</td>";
						echo "<td>";
						echo $task->display_full_task_lesson();
						echo "</td>";
						echo "<td>{$issue->issue_body}</td>";
						echo "<td>" . $logged_in_user->local_time($issue->time_completed) . "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td>No issues have been fixed in the specified period.</td><td></td><td></td><td></td><tr>";
				} ?>
		</table>
		</div>	

		<div id="actionable_tasks">
		<h2 id="main_title">Pending Issues</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Member</th><th>Lesson</th><th>Issue</th></tr>
			<?php 
				if ($actionable_issues) {
					foreach($actionable_issues as $issue) {
						$task = Task::find_by_id($issue->task_id);
						echo "<tr>";
						echo "<td>";
						if($session->is_admin()) {
							echo "<a href='task-sheet.php?member={$task->team_member_id}'>{$task->team_member_name}</a>";
						} else {
							echo $task->team_member_name;
						}
						echo "</td>";
						echo "<td>";
						echo "<img src='images/{$task->level_code}.png'> ";
						echo "<a href='lesson.php?series=".$task->series_id."&langSeries=".$task->language_series_id."&lesson=".$task->lesson_id."'>";
						echo $task->language_name . " - " . $task->series_name . " #" . $task->lesson_number;
						echo "</a>";
						echo "</td>";
						echo "<td>{$issue->issue_body}</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td>No issues have been fixed in the specified period.</td><td></td><td></td><td></td><tr>";
				} ?>
		</table>
		</div>	
		</div>
<?php include_layout_template('footer.php'); ?>