<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	if($_POST['changed_qa']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$qa_log = $db->escape_value($_POST['qa_log']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->qa_log = $qa_log;
		$lesson->update();
		$_SESSION['message'] = "QA Info for ".$lesson->language_name." ".$lesson->series_name." #".$lesson->number. " is now: ".$qa_log;
		redirect_to('qa.php');
	}
	
	$render_queue_lessons = Lesson::get_lessons_for_qa($sort_by);
	
?>

<?php include_layout_template('header.php'); ?>
  <?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	
	<div id="shifts" class="small-12 columns">
		<h3 class="group-heading">QA Lessons</h3>
    <ol class="group">
    <?php
    foreach($render_queue_lessons as $lesson) : ?>
      <div class="qa-group-item">
				<div class="lesson-info">
  				<p class="lesson-title">
          <?php 
    				echo $lesson->language_name . " ";
    				echo $lesson->series_name . " (" . $lesson->level_code . ") ";
    				echo " - #" . $lesson->number; 
  				?>
  				</p>
				</div>
				<div class="qa-input">
  				<div class="row collapse">
            <div class="small-6 medium-5 columns">
              <form action="qa.php" method="post">
              <input type="text" name="qa_log" class="qa" placeholder="The current status of this video" value="<?php echo $lesson->qa_log; ?>">
            </div>
            <div class="small-6 medium-3 columns">
              <input type="text" name="qa_url" class="qa" placeholder="The URL of the checkable video" value="<?php echo $lesson->qa_url; ?>">
            </div>
            <div class="small-12 medium-4 columns">
              <ul class="button-group">
                <li>
                  <input type="hidden" name="qa_lesson_id" value="<?php echo $lesson->id; ?>">
                  <input type="submit" name="changed_qa" class="button" value="Update QA Log">
                  </form>
                </li>
                <li>
                  <a href="issues-for-lesson.php?id=<?php echo $lesson->id; ?>" class="button alert">Add Issue</a>
                </li>
              </ul>
              
            </div>
            <div class="small-6 medium-10 columns">
              
            </div>
          </div>
  		  </div>
  		</div>
    <?php endforeach; ?>
    </ol>
  </div>

<?php include_layout_template('footer.php'); ?>