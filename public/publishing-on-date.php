<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	
	if($_GET['date']) {
		$date = $db->escape_value($_GET['date']);
	} else {
		$date = date('Y-m-d', time());
	}
	$publishing_lessons = Lesson::find_all_lessons_publishing_on_date($date);

?>

<?php include_layout_template('header.php'); ?>
		<h2 class="main_title">Publishing</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
	<div id="publishing-on-date">
	<h3>Lessons set to publish on <?php echo $date; ?></h3>
		<table>
			<tr><th>Lesson</th><th>Status</th><th>View</th><th>Due Date</th></tr>
				<?php 
				if(!$publishing_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($publishing_lessons as $lesson) {
						$issues = Issue::get_unfinished_issues_for_lesson($lesson->id);
						echo "<tr ";
						if (strtotime($lesson->publish_date) < time()) {
							echo "class='overdue'";
						}
						echo ">";
						echo "<td>";
						echo $lesson->display_full_lesson();
						echo "</td>";
						echo "<td><img src='";
						echo $lesson->is_shot ? "images/is_shot.png" : "images/not_shot.png";
						echo "'>";
						echo "<img src='";
						echo $lesson->is_checkable ? "images/is_checkable.png" : "images/not_checkable.png";
						echo "'>";
						echo "<img src='";
						echo $lesson->checked_language ? "images/is_language_checked.png" : "images/not_language_checked.png";
						echo "'>";	
						echo "<img src='";
						echo $lesson->checked_video ? "images/is_video_checked.png" : "images/not_video_checked.png";
						echo "'>";
						echo "<img src='";
						echo $lesson->files_moved ? "images/is_moved.png" : "images/not_moved.png";
						echo "<img src='";
						echo $lesson->is_detected ? "images/is_published.png" : "images/not_published.png";
						echo "'>";
						echo " <a href='issues-for-lesson.php?id=".$lesson->id."'>Issues: ".count($issues)."</a>";					
						echo "</td>";
						echo "<td><a href='lesson.php?series={$lesson->series_id}&langSeries={$lesson->language_series_id}&lesson={$lesson->id}'>View</a>";
						if ($session->is_admin()) {
							echo " | <a href='edit-lesson.php?id={$lesson->id}'>Edit</a>";
						}
						echo "</td>";
						echo "<td>{$lesson->publish_date}</td>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
	</div>
		
<?php include_layout_template('footer.php'); ?>