<?php
	require_once("includes/session.php");
	require_once("includes/connection.php");
	require_once("includes/functions.php");
		
	$lesson_id = mysql_prep($_GET['lesson']);
	$langSeries_id = mysql_prep($_GET['langSeries']);
	$series_id = mysql_prep($_GET['series']);
	
	if(isset($_POST['submit-yt-code'])) {
		$submittedYtCode = $_POST['yt-code'];
		$query = "UPDATE lesson SET ytCode='{$submittedYtCode}' WHERE pkLesson='{$lesson_id}' ";
		$result = mysql_query($query);
		confirm_query($result);
		$message = "Inserted YouTube code: {$submittedYtCode}";
	}
	
	if(isset($_GET['removeYtCode'])) {
		$query = "UPDATE lesson SET ytCode='0' WHERE pkLesson='{$lesson_id}' ";
		$result = mysql_query($query);
		confirm_query($result);
		$message = "Removed the YouTube code from this lesson";
	}
		
	// Task Query
	$task_query =  "SELECT task.pkTask, teamMember.nameFirst AS teamMemberName, taskGlobal.name AS taskName, task.fkTeamMember, task.isActionable, task.isActive, ";
	$task_query .= "task.timeActual AS timeActual ";
	$task_query .= "FROM task ";
	$task_query .= "JOIN teamMember ON task.fkTeamMember=teamMember.pkTeamMember ";
	$task_query .= "JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.pkTaskGlobal ";
	$task_query .= "WHERE fkLesson = {$lesson_id} ";
	
	$task_query_result = mysql_query($task_query);
	confirm_query($task_query_result);
		
	// Lesson Query
	$lesson_query = "SELECT title, ytCode FROM lesson WHERE pkLesson = {$lesson_id} ";
	
	$lesson_query_result = mysql_query($lesson_query);
	confirm_query($lesson_query_result);
	$lesson = mysql_fetch_array($lesson_query_result);
	
?>

<?php include("includes/header.php"); ?>
		<div>
		<h2><?php echo full_lesson_title_for_lesson($lesson_id); ?>
		</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<p><a href="language-series.php?series=<?php echo $series_id; ?>&langSeries=<?php echo $langSeries_id; ?>"><- Return to Language Series List</a></p>
		<table>
			<tr><th>Task Name</th><th>Team Member</th><th>Actionable?</th><th>Active?</th><th>Time Spent</th></tr>
			<?php 
				while ($task = mysql_fetch_array($task_query_result)) {
					echo "<tr>";
					echo "<td>{$task['taskName']}</td>";
					echo "<td>{$task['teamMemberName']}</td>";
					echo "<td>". ($task['isActionable'] ? 'yes' : 'no')."</td>";
					echo "<td>". ($task['isActive'] ? 'yes' : 'no')."</td>";
					echo "<td>{$task['timeActual']}</td>";
					echo "</tr>"; 
					}
				?>
		</table>
		</div></ br>
		<div id="YouTube">
		<?php
			if(empty($lesson['ytCode'])) {
				echo "<p>No YouTube code added.</p>";
				?>
				<form method="post" action="lesson.php?series=<?php echo $series_id; ?>&langSeries=<?php echo $langSeries_id; ?>&lesson=<?php echo $lesson_id; ?>">
					<input type="text" size="11" name="yt-code">
					<input type="submit" name="submit-yt-code">
				</form>
			<?php
			} else {
				echo embed_youtube_video($lesson['ytCode']);
				echo '<br><br><a href="lesson.php?series=' . $series_id . '&langSeries=' . $langSeries_id . '&lesson=' . $lesson_id . '&removeYtCode=yes">Clear YouTube Code</a>';
			}
		?>
				
		</div>
		<p><a href="language-series.php?series=<?php echo $series_id; ?>&langSeries=<?php echo $langSeries_id; ?>"><- Return to Language Series List</a></p>

<?php include("includes/footer.php"); ?>