<?php require_once("../includes/initialize.php"); ?>
<?php		
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	$global_task_id = $db->escape_value($_GET['id']);
	$global_task = GlobalTask::find_by_id($global_task_id);
	$task_stats = GlobalTaskStatistic::find_all_child_for_parent($global_task_id, "task", "TaskGlobal", " AND task.isCompleted = 1 GROUP BY task.fkTeamMember ORDER BY task.timeActual ASC ");
	$completed_tasks = Task::find_all_completed_tasks_from_global_task($global_task_id);
?>

<?php include_layout_template('header.php'); ?>

<div class="small-12 medium-8 medium-centered columns">
  <div id="breadcrumbs" class="row">
  	<ul class="breadcrumbs">
  		<li><a href="lesson-db.php">Lesson DB</a></li>
  		<li><a href="series.php?id=<?php echo $global_task->series_id; ?>"><?php echo $global_task->series_name; ?></a></li>
  		<li><a href="edit-series.php?id=<?php echo $global_task->series_id; ?>">Edit</a></li>
  		<li class="current">
  			<a href="#">
  				View <?php echo $global_task->task_name; ?>
  			</a>
  		</li> 
  	</ul>
  </div>

  <div class="panel">

		<div class="row">
		  <h4><?php echo "{$global_task->task_name}"; ?></h4>
		</div>
		<div class="row">
  		<p>Task Name: <?php echo $global_task->task_name; ?></p>
  		<p>Actionable At: <?php echo $global_task->actionable_at; ?></p>
  		<p>Completion Value: <?php echo $global_task->completion_value; ?></p>
  		<p>Default Team Member: <?php echo $global_task->team_member_name; ?></p>
		</div>
		
		<table>
			<tr><th>Team Member</th><th>Times Completed</th><th>Average Completion Time</th></tr>
			<?php foreach ($task_stats as $task_stat) {
				echo "<tr>";
				echo "<td>{$task_stat->team_member_name}</td>";
				echo "<td>{$task_stat->times_completed}</td>";
				echo "<td>". seconds_to_timecode($task_stat->average_time, 6). "</td>";
				echo "</tr>";
			} ?>
		</table>
		<br />
		<table>
			<tr><th>Task</th><th>Team Member</th><th>Completion Time</th></tr>
			<?php foreach ($completed_tasks as $completed_task) {
				echo "<tr><td>";
				echo $completed_task->display_full_task_lesson() . "</td>";
				echo "<td>{$completed_task->team_member_name}</td>";
				echo "<td>".seconds_to_timecode($completed_task->actual_time, 6)."</td>";
				echo "</tr>";
			} ?>
		</table>
		
		<p><a href="edit-series.php?id=<?php echo $global_task->series_id; ?>><- Return to Series Page</a></p>
		</div>
<?php include_layout_template('footer.php'); ?>
	