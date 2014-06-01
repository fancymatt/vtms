<?php require_once("../includes/initialize.php"); ?>
<?php	
	$series_id = $db->escape_value($_GET['series']);
	$language_series_id = $db->escape_value($_GET['id']);
	$series_lessons = Lesson::find_all_lessons_for_language_series($db->escape_value($_GET['id']));
?>

<?php include_layout_template('header.php'); ?>
		<div>
		<h2><?php echo LanguageSeries::get_language_series_title_from_id($language_series_id); ?></h2>
		<?php echo $session->message(); ?>
		<p><a href="series.php?id=<?php echo $series_id; ?>"><- Return to Language Series List</a></p>
		<table>
			<tr><th></th><th>Lesson Name</th><th>Status</th><th>TRT</th><th>Publish Date</th><th>View</th></tr>
				<?php 
				if(!$series_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($series_lessons as $series_lesson) {
						echo "<tr ";
						if ($series_lesson->files_moved ) {
							echo "class='completed'";
						} else if ($series_lesson->is_shot) {
							echo "class='actionable'";
						} 
						echo ">";
						echo "<td>{$series_lesson->number}</td>";
						echo "<td>{$series_lesson->title}</td>";
						echo "<td><img src='";
						echo $series_lesson->is_shot ? "images/is_shot.png" : "images/not_shot.png";
						echo "'>";
						echo "<img src='";
						echo $series_lesson->is_checkable ? "images/is_checkable.png" : "images/not_checkable.png";
						echo "'>";
						echo "<img src='";
						echo $series_lesson->checked_language ? "images/is_language_checked.png" : "images/not_language_checked.png";
						echo "'>";	
						echo "<img src='";
						echo $series_lesson->checked_video ? "images/is_video_checked.png" : "images/not_video_checked.png";
						echo "'>";
						echo "<img src='";
						echo $series_lesson->files_moved ? "images/is_moved.png" : "images/not_moved.png";
						echo "'>";					
						echo "</td>";
						echo "<td>".seconds_to_timecode($series_lesson->trt)."</td>";
						echo "<td>".$series_lesson->publish_date ."</td>";
						echo "<td><a href='lesson.php?series={$series_lesson->series_id}&langSeries={$series_lesson->language_series_id}&lesson={$series_lesson->id}'>View</a>";
						if ($session->is_admin()) {
							echo " | <a href='edit-lesson.php?id={$series_lesson->id}'>Edit</a>";
						}
						echo "</td></tr>";
					}
				}
				if ($session->is_admin()) {
					echo "<tr><td><a href='new-lesson.php?inLanguageSeries={$language_series_id}'>Add New Lesson</a></td></tr>";	
				} ?>	
		</table>
		<p><a href="series.php?id=<?php echo $series_id; ?>"><- Return to Language Series List</a></p>
		</div>

<?php include_layout_template('footer.php'); ?>