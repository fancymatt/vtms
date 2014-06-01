<?php require_once("../includes/initialize.php"); ?>
<?php	
	$languageSeries = LanguageSeries::find_all_language_series_for_series($db->escape_value($_GET['id']));
?>
<?php include_layout_template('header.php'); ?>
		<div>
		
		
		<h3><?php echo Series::get_series_title_from_id($db->escape_value($_GET['id'])); ?></h3>
		<p><a href="lesson-db.php"><- Return to Series List</a></p>
		<table>
			<tr><th>Title</th><th>Series TRT</th><th>Actions</th></tr>
			<?php 
				foreach($languageSeries as $series) {
					echo "<tr>";
					echo "<td>";
					echo $series->display_full_language_series();
					echo "</td>";
					echo "<td>".$series->total_trt."</td>";
					echo "<td><a href='language-series.php?series={$series->series_id}&id={$series->id}'>View</a>";
					if ($session->is_admin()) {
						echo " | <a href='edit-language-series.php?id={$series->id}'>Edit</a>";
					}
					echo "</td></tr>";
				}
				if ($session->is_admin()) {
					echo "<tr><td><a href='new-language-series.php?inSeries={$db->escape_value($_GET['id'])}'>Add New Language Series</a></td></tr>";
				} ?>
		</table>
		<p><a href="lesson-db.php"><- Return to Series List</a></p>
		</div>

<?php include_layout_template('footer.php'); ?>