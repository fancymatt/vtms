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
  				<div class="lesson-info">
    				<p class="lesson-title"><?php echo $lesson->display_full_lesson(); ?></p>
    				<p class="lesson-title"><?php echo $lesson->title; ?></p>
    				<p class="date"><?php echo "Due ".$lesson->publish_date; ?></p>
  				</div>
  				<?php $lesson->display_lesson_status_bar(); ?>
    		</div>
      <?php endforeach; ?>
      </ol>
      <?php } ?>
    </div>

<?php include_layout_template('footer.php'); ?>