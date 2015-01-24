<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$lessons_ready = Lesson::find_all_lessons_that_need_testing_on_ill_tv();
	$logged_in_user = User::find_by_id($session->user_id);
	
	if(isset($_POST['mark_lesson_as_checked'])) {
  	$current_time = new DateTime(null, new DateTimeZone('UTC'));
	  $lesson_id = $db->escape_value($_POST['lesson_id']);
		$lesson = Lesson::find_by_id($lesson_id);
	  $lesson->ill_tv_test_date = $current_time->format('Y-m-d H:i:s');
		$lesson->ill_tv_is_tested = 1;
		$lesson->update();
		$_SESSION['message'] = "You've marked that video as playing properly.";
		redirect_to('ready-to-test-ill-tv.php');
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
    <h3 class="group-title">Ready to Test on ILL TV</h3>
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
      				<div class="small-6 columns">
      				</div>
              <div class="small-6 columns">
                <ul class="actions">
        					<li class="action-item">
        						<form action='ready-to-test-ill-tv.php' method='post'>
        							<input type='hidden' name='lesson_id' value='<?php echo $lesson->id; ?>'>
        							<button type="submit" class="no-format" name="mark_lesson_as_checked" data-tooltip class="has-tip" title="Publish Lesson"><img src="img/icon-move-files.png"></button>
        						</form>
        					</li>
        				</ul>
              </div>
  			    </div>
          </div>
    		</div>
      <?php endforeach; ?>
      </ol>
      <?php } ?>

</div>

<?php include_layout_template('footer.php'); ?>