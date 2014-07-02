<?php
	require_once("includes/session.php");
	require_once("includes/connection.php");
	require_once("includes/functions.php");
	
	confirm_logged_in();
	
	$units = find_all_story_units();
	
?>

<?php include("includes/header.php"); ?>
		<div>
		<?php echo message(); ?>
		<h2>Manage Can Do story</h2>
		<table>
			<tr><th>Level</th><th>Unit</th><th>Summary</th></tr>
			<?php while ($unit = mysql_fetch_array($units)) {
				echo "<tr>";
				echo "<td>{$unit['lev']}</td>";
				echo "<td>{$unit['unit']}</td>";
				echo "<td>{$unit['summary']}</td>";
				echo "<td><a href='viewStoryUnit.php?unit={$unit['id']}'>View</a></td>";
				} ?>
			</table>
		</div>

<?php include("includes/footer.php"); ?>