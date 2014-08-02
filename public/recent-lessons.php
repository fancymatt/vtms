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

    <div id="upcoming-lessons" class="medium-12 large-6 columns">
  		<h3 class="group-heading">Upcoming Lessons</h3>
      <?php
      if($upcoming_lessons) { ?>
      <ol class="group">
      <?php
      foreach($upcoming_lessons as $lesson) : ?>
        <div class="group-item<?php if (strtotime($upcoming_lesson->publish_date) < time()) { echo " overdue"; } ?>">
          <?php $lesson->display_lesson_status_bar(); ?>
			    <div class="lesson-info">
    				<a class="lesson-title" href="lesson.php?id=<?php echo $lesson->id; ?>"><?php echo $lesson->display_full_lesson(); ?></a>
    				<p class="date"><?php echo "Due ".$lesson->publish_date; ?></p>
  				</div>
    		</div>
      <?php endforeach; ?>
      </ol>
      <?php } ?>
    </div>

<?php include_layout_template('footer.php'); ?>