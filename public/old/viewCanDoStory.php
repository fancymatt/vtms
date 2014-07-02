<?php
	require_once("includes/session.php");
	require_once("includes/connection.php");
	require_once("includes/functions.php");
		
	$story_id = mysql_prep($_GET['story']);
	$series_id = mysql_prep($_GET['unit']);
			
	// Lines Query
	$query  = "SELECT pkStoryLine, fkStory, number, type, text, cdCharacter.name AS name ";
	$query .= "FROM cdStoryLine ";
	$query .= "LEFT JOIN cdCharacter ON cdCharacter.pkCharacter=cdStoryLine.fkCharacter ";
	$query .= "WHERE fkStory = {$story_id} ";
	$query .= "ORDER BY number ASC ";
	
	$query_result = mysql_query($query);
	confirm_query($query_result);
	
?>

<?php include("includes/header.php"); ?>
		<div>
		<h2>Header</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<p><a href="viewStoryUnit.php?unit=<?php echo $series_id; ?>"><- Return to Can Do List</a></p>
		<table>
			<tr><th>Character</th><th>Action/Dialogue</th></tr>
			<?php 
				while ($line = mysql_fetch_array($query_result)) {
					echo "<tr>";
					if ($line['type'] = "dialogue") {
						echo "<td>{$line['name']}</td>";
						echo "<td>{$line['text']}</td>";
					} else {
						echo "<td></td>";
						echo "<td>{$line['text']}</td>";
					}
					echo "</tr>"; 
					}
				?>
		</table>
		</div></ br>
		
		<?php include("includes/footer.php"); ?>