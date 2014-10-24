<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$days_ahead = 60;
	$upcoming_lessons = Lesson::find_all_upcoming_due_lessons($days_ahead);
?>

<?php include_layout_template('header.php'); ?>

<div class="small-12 medium-8 medium-centered columns">
	<h3 class="group-heading">All Overdue and Upcoming Lessons</h3>
  <?php
  if($upcoming_lessons) { ?>
  <ol class="group">
  <?php
  foreach($upcoming_lessons as $lesson) : ?>
		<div class="group-item<?php if (strtotime($lesson->publish_date) < time()) { echo " overdue"; } ?>">
      <?php $lesson->display_lesson_status_bar(); ?>
      <div class="group-item-body">
		    <div class="group-item-content">
		       <div class="lesson-info">
    				<a class="lesson-title" href="lesson.php?id=<?php echo $lesson->id; ?>"><?php echo $lesson->display_full_lesson(); ?></a>
  				</div>
          <div class="group-item-metadata">
            <p><?php echo "Publish on ".$lesson->publish_date; ?></p>
             <p><?php echo "QA Log: ".$lesson->qa_log; ?></p>
          </div>
		    </div>
      </div>
		</div>
  <?php endforeach; ?>
  </ol>
  <?php } ?>
</div>

<?php include_layout_template('footer.php'); ?>