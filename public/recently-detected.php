<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$days_past = 1;
	$lessons_recent = Lesson::get_recently_detected_lessons(25);
	$logged_in_user = User::find_by_id($session->user_id);
?>

<?php include_layout_template('header.php'); ?>

<div id="tasks-actionable" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Recently Detected lessons</h3>
    <div class="group-item-sort-options">
    </div>
  </div>
<?php if($lessons_recent) { ?>
      <ol class="group">
      <?php
      foreach($lessons_recent as $lesson) : ?>
				<div class="group-item">
          <div class="group-item-body">
  			    <div class="group-item-content">
  			       <div class="lesson-info">
        				<a class="lesson-title" href="lesson.php?id=<?php echo $lesson->id; ?>"><?php echo $lesson->display_full_lesson(); ?></a>
      				</div>
              <div class="group-item-metadata">
                <p>
                  <?php 
                    if($lesson->detected_time > 0) {
                      echo "Detected ";
                      echo date("M jS g:i a", strtotime($logged_in_user->local_time($lesson->detected_time)));
                    }
                  ?>
                </p>
              </div>
  			    </div>
          </div>
    		</div>
      <?php endforeach; ?>
      </ol>
      <?php } ?>

</div>

<?php include_layout_template('footer.php'); ?>