<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$days_ahead = 180;
	$upcoming_lessons = Lesson::find_all_upcoming_due_lessons($days_ahead);
?>

<?php include_layout_template('header.php'); ?>

    <div class="row">
  		<div class="small-12 columns">
  		  <h3>Lesson Admin</h3>
  		</div>
		</div>

    <div id="upcoming-lessons" class="small-12 columns">
  		<h3 class="group-heading">Upcoming Lessons</h3>
      <?php
      if($upcoming_lessons) { ?>
      <ol class="group">
      <?php
      foreach($upcoming_lessons as $lesson) : ?>
        <div class="group-item">
  				<div class="lesson-info">
    				<p class="lesson-title"><?php echo $lesson->display_full_lesson(); ?></p>
    				<p class="lesson-title"><?php echo $lesson->title; ?></p>
    				<p class="date"><?php echo "Due ".$lesson->publish_date; ?></p>
  				</div>
  				<div class="lesson-status">
  				  <p class="lesson-status-item">
  				    <img src="<?php echo $lesson->is_shot ? "img/lesson-status-yes-shot.png" : "img/lesson-status-not-shot.png"?>">
  				  </p>
  				  <p class="lesson-status-item">
  				    <img src="<?php echo $lesson->is_checkable ? "img/lesson-status-yes-checkable.png" : "img/lesson-status-not-checkable.png"?>">
  				  </p>
  				  <p class="lesson-status-item">
  				    <img src="<?php echo $lesson->checked_language ? "img/lesson-status-yes-language.png" : "img/lesson-status-not-language.png"?>">
  				  </p>
  				  <p class="lesson-status-item">
  				    <img src="<?php echo $lesson->checked_video ? "img/lesson-status-yes-video.png" : "img/lesson-status-not-video.png"?>">
  				  </p>
  				  <p class="lesson-status-item">
  				    <img src="<?php echo $lesson->files_moved ? "img/lesson-status-yes-moved.png" : "img/lesson-status-not-moved.png"?>">
  				  </p>
  				</div>
  				<div class="actions">
  				  <a class="action-item" href="#"><img src="img/icon-add-issue.png"></a>
  					<a class="action-item" href="#"><img src="img/icon-add-issue.png"></a>
  				</div>
    		</div>
      <?php endforeach; ?>
      </ol>
      <?php } ?>
    </div>


		<div id="upcoming_lessons">
		<h2 id="main_title">Upcoming Lessons</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Lesson</th><th>Status</th><th>View</th><th>Due Date</th></tr>
				<?php 
				/*
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
				 }
*/ ?>		
		</table>
		</div>
<?php include_layout_template('footer.php'); ?>