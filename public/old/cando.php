<?php
	require_once("includes/connection.php");
	require_once("includes/session.php");
	require_once("includes/functions.php");
	
	$query  = "SELECT language.name AS lang, level.name AS lev, pkLanguageSeries AS id FROM languageSeries ";
	$query .= "JOIN language ON language.pkLanguage=languageSeries.fkLanguage ";
	$query .= "JOIN level ON level.pkLevel=languageSeries.fkLevel ";
	$query .= "WHERE languageSeries.fkSeries = 11 ";
	
	$result = mysql_query($query);
	confirm_query($result);
	
?>

<?php include("includes/header.php"); ?>
		<div>
		<h2>Series List</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Name</th><th>View</th><th>View</th></tr>
			<?php while ($row = mysql_fetch_array($result)) {
				echo "<tr>";
				echo "<td>{$row['lang']}</td>";
				echo "<td>{$row['lev']}</td>";
				echo "<td><a href='viewCanDoSeries.php?series={$row['id']}'>View</td>";
				echo "</tr>";
				} ?>
		</table>
		</div>

<?php include("includes/footer.php"); ?>