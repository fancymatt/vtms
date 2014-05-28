<?php
	require_once("includes/session.php");
	require_once("includes/connection.php");
	require_once("includes/functions.php");
		
	$story_unit = mysql_prep($_GET['unit']);
	
	// Story Query
	$story_query  = "SELECT cdStory.pkStory AS id, level.name AS level, cdStory.number AS num, cdStory.summary AS sum, cdStory.location AS location ";
	$story_query .= "FROM cdStory ";
	$story_query .= "JOIN cdStoryUnit ON cdStoryUnit.pkStoryUnit=cdStory.fkStoryUnit ";
	$story_query .= "JOIN level ON level.pkLevel=cdStoryUnit.fkLevel ";
	$story_query .= "WHERE fkStoryUnit = {$story_unit} ";
	
	$story_query_result = mysql_query($story_query);
	confirm_query($story_query_result);
	
?>

<?php include("includes/header.php"); ?>
		<div>
		<h2>Story Unit</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<p><a href="manageCanDoStory.php"><- Return to Manage Can Do Story</a></p>
		<table>
			<tr><th>Number</th><th>Location</th><th>Summary</th><th>View</th></tr>
			<?php 
				while ($story = mysql_fetch_array($story_query_result)) {
					echo "<tr>";
					echo "<td>{$story['num']}</td>";
					echo "<td>{$story['location']}</td>";
					echo "<td>{$story['sum']}</td>";
					echo "<td><a href='viewCanDoStory.php?unit={$story_unit}&story={$story['id']}'>View</a></td>";
					echo "</tr>"; 
					}
				?>
		</table>
		<p><a href="manageCanDoStory.php"><- Return to Manage Can Do Story</a></p>
		</div></ br>

<?php include("includes/footer.php"); ?>