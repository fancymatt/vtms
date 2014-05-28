<?php
	require "appConfig.php";
	require_once("includes/functions.php");
		
	$series_id = $_GET['series'];
	$langSeries_id = $_GET['langSeries'];
	$task_id = $_GET['task'];
		
	$query =  "SELECT language.name AS language_name, series.title AS series_name, lesson.number AS lesson_number, taskGlobal.name AS task_name, teamMember.nameFirst AS member_name ";
	$query .= "FROM task ";
	$query .= "JOIN taskGlobal ON taskGlobal.pkTaskGlobal=task.fkTaskGlobal ";
	$query .= "JOIN lesson ON lesson.pkLesson=task.fkLesson ";
	$query .= "JOIN languageSeries ON languageSeries.pkLanguageSeries=lesson.fkLanguageSeries ";
	$query .= "JOIN series ON series.pkSeries=languageSeries.fkSeries ";
	$query .= "JOIN language ON language.pkLanguage=languageSeries.fkLanguage ";
	$query .= "JOIN teamMember ON task.fkTeamMember=teamMember.pkTeamMember ";
	$query .= "WHERE task.fkTaskGlobal = 1 ";
	$query .= "ORDER BY language.name, languageSeries.seriesTitle, lesson.number ASC ";
		
	$result = mysql_query($query);
	
	if (!$result) {
		die("<p>Error in listing tables: " . mysql_error() . "</p>");
	}
	
?>
<html>
	<head>
	<link rel="stylesheet" href="styles/main.css">
	</head>
	<body>
		<div>
		<h2>Actionable Tasks</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<p><a href="seriesList.php"><- Return to Top</a></p>
		<table>
			<tr><th>Language</th><th>Series</th><th>#</th><th>Task</th><th>Team Member</th></tr>
			<?php while ($row = mysql_fetch_array($result)) {
				echo "<tr>";
				echo "<td>{$row['language_name']}</td>";
				echo "<td>{$row['series_name']}</td>";
				echo "<td>{$row['lesson_number']}</td>";
				echo "<td>{$row['task_name']}</td>";
				echo "<td>{$row['member_name']}</td>";
				echo "</tr>";
				} ?>
		</table>
		<p><a href="seriesList.php"><- Return to Top</a></p>
		</div>
	</body>
</html>