<?php require_once("../includes/initialize.php"); ?>
<?php $series = Series::find_all_limit(0); ?>

<?php include_layout_template('header.php'); ?>
		<div>
		<h2>Series List</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Name</th><th>TRT</th><th>Actions</th></tr>
			<?php foreach($series as $row) {
				echo "<tr>";
				echo "<td>{$row->title}</td>";
				echo "<td>".$row->series_trt."</td>";
				echo "<td><a href='series.php?id={$row->id}'>View</a>";
				if ($session->is_admin()) {
					echo " | <a href='edit-series.php?id={$row->id}'>Edit</a>";
				}
				echo "</td></tr>";
			} ?>
			<tr><td><a href="new-series.php">Add new Series</a></td><td></td></tr>
		</table>
		</div>

<?php include_layout_template('footer.php'); ?>