<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$days_ahead = 180;
	$upcoming_lessons = Lesson::find_all_upcoming_due_lessons($days_ahead);
?>

<?php include_layout_template('header.php'); ?>
		<div id="upcoming_lessons">
		<h2 id="main_title">Upcoming Lessons</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Lesson</th><th>Status</th><th>View</th><th>Due Date</th></tr>
				<?php 
				if(!$upcoming_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($upcoming_lessons as $upcoming_lesson) {
						$issues = Issue::get_unfinished_issues_for_lesson($upcoming_lesson->id);
						echo "<tr ";
						if (strtotime($upcoming_lesson->publish_date) < time()) {
							echo "class='overdue'";
						}
						echo ">";
						echo "<td>";
						echo $upcoming_lesson->display_full_lesson();
						echo "</td>";
						echo "<td><img src='";
						echo $upcoming_lesson->is_shot ? "images/is_shot.png" : "images/not_shot.png";
						echo "'>";
						echo "<img src='";
						echo $upcoming_lesson->is_checkable ? "images/is_checkable.png" : "images/not_checkable.png";
						echo "'>";
						echo "<img src='";
						echo $upcoming_lesson->checked_language ? "images/is_language_checked.png" : "images/not_language_checked.png";
						echo "'>";	
						echo "<img src='";
						echo $upcoming_lesson->checked_video ? "images/is_video_checked.png" : "images/not_video_checked.png";
						echo "'>";
						echo "<img src='";
						echo $upcoming_lesson->files_moved ? "images/is_moved.png" : "images/not_moved.png";
						echo "'>";
						echo " <a href='issues-for-lesson.php?id=".$upcoming_lesson->id."'>Issues: ".count($issues)."</a>";					
						echo "</td>";
						echo "<td><a href='lesson.php?series={$upcoming_lesson->series_id}&langSeries={$upcoming_lesson->language_series_id}&lesson={$upcoming_lesson->id}'>View</a>";
						if ($session->is_admin()) {
							echo " | <a href='edit-lesson.php?id={$upcoming_lesson->id}'>Edit</a>";
						}
						echo "</td>";
						echo "<td>{$upcoming_lesson->publish_date}</td>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
		</div>
<?php include_layout_template('footer.php'); ?>