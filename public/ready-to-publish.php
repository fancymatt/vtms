<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$lessons_ready = Lesson::get_ready_to_publish_lessons();
	$logged_in_user = User::find_by_id($session->user_id);
	
	if(isset($_POST['mark_lesson_as_published'])) {
		$lesson_id = $db->escape_value($_POST['lesson_id']);
		$lesson = Lesson::find_by_id($lesson_id);
		$current_time = new DateTime(null, new DateTimeZone('UTC'));
	  $lesson->detected_time = $current_time->format('Y-m-d H:i:s');
	  $lesson->is_detected = 1;
		$lesson->update();
		$_SESSION['message'] = "You've marked that video as published.";
		redirect_to('ready-to-publish.php');
	}
	
?>

<?php include_layout_template('header.php'); ?>

<?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
<?php } ?>

<div id="tasks-actionable" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Ready to Publish lessons</h3>
    <div class="group-item-sort-options">
    </div>
  </div>
<?php if($lessons_ready) { ?>
      <ol class="group">
      <?php
      foreach($lessons_ready as $lesson) : ?>
				<div class="group-item">
          <div class="group-item-body">
  			    <div class="group-item-content">
  			      <div class="lesson-info">
        				<a class="lesson-title" href="lesson.php?id=<?php echo $lesson->id; ?>"><?php echo $lesson->display_full_lesson(); ?></a>
      				</div>
              <div class="group-item-metadata">
                <p>
                  <?php 
                  echo "Due ";
                  echo $lesson->publish_date;
                  ?>
                </p>
              </div>
              <ul class="actions">
      					<li class="action-item">
      						<form action='ready-to-publish.php' method='post'>
      							<input type='hidden' name='lesson_id' value='<?php echo $lesson->id; ?>'>
      							<button type="submit" class="no-format" name="mark_lesson_as_published" data-tooltip class="has-tip" title="Publish Lesson"><img src="img/icon-move-files.png"></button>
      						</form>
      					</li>
      				</ul>
  			    </div>
          </div>
    		</div>
      <?php endforeach; ?>
      </ol>
      <?php } ?>

</div>

<?php include_layout_template('footer.php'); ?>