<?php
	require_once("includes/connection.php");
	require_once("includes/functions.php");
	require_once("includes/session.php");
	
	$series_id = mysql_prep($_GET['series']);
		
	$query =  "SELECT pkLesson AS lesson_id, number AS lesson_number, title AS lesson_title, fkLanguageSeries, TIME_FORMAT(trt, '%i:%s') AS lessonTRT, ytCode ";
	$query .= "FROM lesson ";
	$query .= "WHERE fkLanguageSeries = {$series_id} ";
		
	$result = mysql_query($query);
	confirm_query($result);
	$series_lessons = mysql_num_rows($result);
	
?>

<?php include("includes/header.php"); ?>
		<div>
		<h2><?php echo languageseries_title_for_languageseries($series_id); ?></h2>
		<?php if ($message) {
			echo "<div><p>{$message}</p></div>";
		} ?>
		<p><a href="cando.php"><- Return to Can Do List</a></p>
		<table>
			<tr><th></th><th>Lesson Name</th><th>TRT</th><th>YT-Code</th><th>View</th></tr>
				<?php 
				if(!$series_lessons) {
					echo "<td>No lessons</td>";
				} else {
					while ($row = mysql_fetch_array($result)) { ?>
						<tr>
						<td><?php echo $row['lesson_number']; ?></td>
						<td><?php echo $row['lesson_title']; ?></td>
						<td><?php echo $row['lessonTRT']; ?></td>
						<td><?php echo (!empty($row['ytCode']) ? "Set" : "Not set"); ?></td>
						<td><a href="<?php echo "viewCanDoLesson.php?langSeries={$series_id}&lesson={$row['lesson_id']}"; ?>">View</a></td>
						</tr>
				<?php } 
				 } ?>		
		</table>
		<p><a href="cando.php"><- Return to Can Do List</a></p>
		</div>

<?php include("includes/footer.php"); ?>