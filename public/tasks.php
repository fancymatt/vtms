<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$days_past = 1;
	$recent_tasks = Task::get_recently_completed_tasks($days_past);
	$active_tasks = Task::get_all_active_tasks();
	$actionable_tasks = Task::get_all_actionable_tasks();
	$logged_in_user = User::find_by_id($session->user_id);
	
?>

<?php include_layout_template('header.php'); ?>
		<div id="active_tasks">
		<h2 id="main_title">Active Tasks</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Member</th><th>Lesson</th><th>Task</th><th>Time Activated</th></tr>
			<?php 
				if ($active_tasks) {
					foreach($active_tasks as $task) {
					echo "<tr>";
					echo "<td>";
					if($session->is_admin()) {
						echo "<a href='task-sheet.php?member={$task->team_member_id}'>{$task->team_member_name}</a>";
					} else {
						echo $task->team_member_name;
					}
					echo "</td>";
					echo "<td>";
					echo $task->display_full_task_lesson();
					echo "</td>";
					echo "<td>{$task->task_name}</td>";
					echo "<td>" . $logged_in_user->local_time($task->activated_time) . "</td>";
					echo "</tr>";
					}
				} else {
					echo "<tr><td>No active tasks at the moment.</td><td></td><td></td><td></td><tr>";
				} ?>
		</table>
		</div>
		
		<div id="recent_tasks">
		<h2 id="main_title">Tasks Completed in the last <?php if ($days_past == 1) { echo "day"; } else { echo $days_past . " days"; } ?></h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Member</th><th>Lesson</th><th>Task</th><th>Completed Time</th></tr>
			<?php 
				if ($recent_tasks) {
					foreach($recent_tasks as $task) {
					echo "<tr>";
					echo "<td>";
					if($session->is_admin()) {
						echo "<a href='task-sheet.php?member={$task->team_member_id}'>{$task->team_member_name}</a>";
					} else {
						echo $task->team_member_name;
					}
					echo "</td>";
					echo "<td>";
					echo $task->display_full_task_lesson();
					echo "</td>";
					echo "<td>{$task->task_name}</td>";
					echo "<td>" . $logged_in_user->local_time($task->completed_time) . "</td>";
					echo "</tr>";
					}
				} else {
					echo "<tr><td>No tasks have been completed in the specified period.</td><td></td><td></td><td></td><tr>";
				} ?>
		</table>
		</div>	

		<div id="actionable_tasks">
		<h2 id="main_title">Actionable Tasks</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Member</th><th>Lesson</th><th>Task</th><th>Due Date</th></tr>
			<?php if ($actionable_tasks) {
				foreach($actionable_tasks as $task) {
				echo "<tr ";
						if (strtotime($task->task_due_date) < time()) {
							echo "class='overdue'";
						}
						echo ">";
				echo "<td>";
					if($session->is_admin()) {
						echo "<a href='task-sheet.php?member={$task->team_member_id}'>{$task->team_member_name}</a>";
					} else {
						echo $task->team_member_name;
					}
					echo "</td>";
				echo "<td>";
				echo $task->display_full_task_lesson();
				echo "</td>";
				echo "<td>{$task->task_name}</td>";
				echo "<td>" . $task->task_due_date . "</td>";
				echo "</tr>";
				}
			} else {
				echo "<tr><td>No actionable tasks at the moment.</td><td></td><td></td><td></td><tr>";
			} ?>
		</table>
		</div>
<?php include_layout_template('footer.php'); ?>