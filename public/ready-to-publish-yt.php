<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$lessons_ready = Lesson::get_ready_to_publish_youtube_lessons();
	$logged_in_user = User::find_by_id($session->user_id);
	
	if(isset($_POST['mark_lesson_as_uploaded'])) {
		$lesson_id = $db->escape_value($_POST['lesson_id']);
		$lesson = Lesson::find_by_id($lesson_id);
		$current_time = new DateTime(null, new DateTimeZone('UTC'));
	  $lesson->yt_uploaded_time = $current_time->format('Y-m-d H:i:s');
	  $lesson->is_uploaded_yt = 1;
		$lesson->update();
		$_SESSION['message'] = "You've marked that video as uploaded to YouTube.";
		redirect_to('ready-to-publish-yt.php');
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
      				<div class="small-6 columns">
                <div class="group-item-metadata">
                  <p>
                    <?php 
                    echo "YouTube Publishing Date: ";
                    echo "<strong>".$lesson->publish_date_yt."</strong>";
                    ?>
                  </p>
                  <?php 
                  if ($lesson->checked_language_time > 0) {
                    echo "<p>";
                    echo "Language Checked: ";
                    echo "<strong>";
                    echo date("M jS g:i a", strtotime($logged_in_user->local_time($lesson->checked_language_time)));
                    echo "</strong>";
                    echo "</p>";
                  }
                  ?>
                  <?php 
                  if ($lesson->checked_video_time > 0) {
                    echo "<p>";
                    echo "Video Checked: ";
                    echo "<strong>";
                    echo date("M jS g:i a", strtotime($logged_in_user->local_time($lesson->checked_video_time)));
                    echo "</strong>";
                    echo "</p>";
                  }
                  ?>
                  <?php 
                  if ($lesson->files_moved_time > 0) {
                    echo "<p>";
                    echo "Files Moved: ";
                    echo "<strong>";
                    echo date("M jS g:i a", strtotime($logged_in_user->local_time($lesson->files_moved_time)));
                    echo "</strong>";
                    echo "</p>";
                  }
                  ?>
                </div>
      				</div>
              <div class="small-6 columns">
                <ul class="actions">
        					<li class="action-item">
        						<form action='ready-to-publish-yt.php' method='post'>
        							<input type='hidden' name='lesson_id' value='<?php echo $lesson->id; ?>'>
        							<button type="submit" class="no-format" name="mark_lesson_as_uploaded" data-tooltip class="has-tip" title="Upload to YT"><img src="img/icon-move-files.png"></button>
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