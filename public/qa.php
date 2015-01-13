<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	if($_POST['changed_qa']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$qa_log = $db->escape_value($_POST['qa_log']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->qa_log = $qa_log;
		$lesson->update();
		$_SESSION['message'] = "QA Info for ".$lesson->language_name." ".$lesson->series_name." #".$lesson->number. " is now: ".$qa_log." (".$qa_url.")";
		redirect_to('qa.php');
	}
	
	$sort_by = $db->escape_value($_GET['sort']);
	$qa_lessons = Lesson::find_all_checkable_lessons($sort_by);
?>

<?php include_layout_template('header.php'); ?>

  <?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	
  <div id="page-header" class="row">
		<div class="small-12 columns">
			<h3>QA Lessons</h3>
		</div>
	</div>
	
	<div class="row">
    <div id="admin-qa" class="small-12 columns">
  		<h3 class="group-heading">Waiting for Language Check</h3>
      <?php
      if($qa_lessons) { ?>
      <ol class="group">
      <?php
					foreach($qa_lessons as $lesson) : ?>
					<div class="group-item<?php if (strtotime($lesson->publish_date) < time()) { 
                                      echo " overdue"; 
                                      } else if (strpos(strtolower($lesson->qa_log), "approved") !== false) {
                                      echo " ready";
                                      } ?>">
  		    <div class="lesson-info">
    				<a class="lesson-title" href="lesson.php?id=<?php echo $lesson->id; ?>"><?php echo $lesson->display_full_lesson(); ?></a>
    				<div class="qa-status">
    				  <div class="small-2 columns">
    				    <form action='qa.php' method='post'>
      				  <label for="qa-log" class="inline">QA Log: </label>
      				  <label for="qa-url" class="inline">QA URL: </label>
    				  </div>
    				  <div class="small-10 columns">
      				  <input type="text" name="qa_log" size=40 value="<?php echo $lesson->qa_log; ?>">
                <input type="text" name="qa_url" size=40 value="<?php echo $lesson->qa_url; ?>">
    				  </div>
    				</div>
    				<p class="date"><?php echo "Due ".$lesson->publish_date; ?></p>
    				<p class="date"><?php echo "Exported ".$lesson->exported_time; ?></p>
  				</div>
  				<div class="actions">
  				  <li class="action-item">
  				    <a href="issues-for-lesson.php?id=<?php echo $lesson->id; ?>">Add Issue</a>
  				  </li>
  					<li class="action-item">
							<input type='hidden' name='qa_lesson_id' value='<?php echo $lesson->id; ?>'>
              <input type="submit" value="Update QA" class="no-format" name="changed_qa" data-tooltip class="has-tip" title="Update QA Log">
  						</form>
  					</li>
    			</div>
        </div>
      <?php endforeach; ?>
      </ol>
      <?php } else { ?>
        <div class="group-item">
          <div class="lesson-info">
            <a class="lesson-title" href="#">No QA Lessons at the moment.</a>
          </div>
        </div>
      <?php } ?>
    </div>
	</div>
					
<?php include_layout_template('footer.php'); ?>