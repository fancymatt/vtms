<?php
	require_once("includes/session.php");
	require_once("includes/connection.php");
	require_once("includes/functions.php");
	
	$series_id = mysql_prep($_GET['series']);
	
	$query =  "SELECT languageSeries.pkLanguageSeries, languageSeries.seriesTitle, level.code AS level_code ";
	$query .= "FROM languageSeries ";
	$query .= "JOIN level ON languageSeries.fkLevel=level.pkLevel ";
	$query .= "WHERE fkSeries = {$series_id} ";
	$query .= "GROUP BY seriesTitle ASC ";
		
	$result = mysql_query($query);
	confirm_query($result);
	
?>

<?php include("includes/header.php"); ?>
		<div>
		<?php echo message(); ?>
		<h2><?php echo series_title_for_series($series_id); ?></h2>
		<p><a href="seriesList.php"><- Return to Series List</a></p>
		<table>
			<tr><th>Series Title</th><th>Series TRT</th><th>View</th></tr>
			<?php while ($row = mysql_fetch_array($result)) {
				echo "<tr>";
				echo "<td>";
				echo "<img src='images/{$row['level_code']}.png'> ";
				echo "{$row['seriesTitle']}</td>";
				echo "<td>{$row['seriesTRT']}</td>";
				echo "<td><a href='viewLanguageSeries.php?series={$_GET['series']}&langSeries={$row['pkLanguageSeries']}'>View</a></td>";
				echo "</tr>";
				} ?>
			<tr><td><a href="newLanguageSeries.php?series=<?php echo $series_id; ?>">Add New Language</a></td><td></td><td></td></tr>
		</table>
		<p><a href="seriesList.php"><- Return to Series List</a></p>
		</div>

<?php include("includes/footer.php"); ?>