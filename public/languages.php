<?php require_once("../includes/initialize.php"); ?>
<?php
	$languages = Language::find_all();
?>

<?php include_layout_template('header.php'); ?>
		<h2 class="main_title">Languages</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
	<div id="languages">
	<p>Note: Only includes lessons made after 2011, starting with Japanese's Kantan Kana series.</p>
		<table>
			<tr><th>Language</th><th>Completed Lessons</th><th>TRT</th></tr>
				<?php 
				if(!$languages) {
					echo "<td>No lessons</td>";
				} else {
					foreach($languages as $language) {
						echo "<tr>";
						echo "<td>";
						echo $language->name;
						echo "</td>";
						echo "<td>";
						echo $language->lesson_count;
						echo "</td>";
						echo "<td>";
						echo $language->language_trt;
						echo "</td>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
	</div>
		
<?php include_layout_template('footer.php'); ?>