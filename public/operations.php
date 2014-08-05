<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	
	if($_POST['changed_qa']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$qa_log = $db->escape_value($_POST['qa_log']);
		$qa_url = $db->escape_value($_POST['qa_url']);
		$message = "QA Log changed to: " . $qa_log;
		$message = "QA URL changed to: " . $qa_url;
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->qa_log = $qa_log;
		$lesson->qa_url = $qa_url;
		$lesson->update();
		$_SESSION['message'] = "QA Info for ".$lesson->language_name." ".$lesson->series_name." #".$lesson->number. " is now: ".$qa_log." (".$qa_url.")";
		redirect_to('operations.php');
	}
	
	if($_POST['marked_lesson_language_checked']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->checked_language = 1;
		$lesson->update();
	}
	
	if($_POST['add_to_dropbox']) {
		$dropbox_lesson_id = $db->escape_value($_POST['dropbox_lesson_id']);
		$lesson = Lesson::find_by_id($dropbox_lesson_id);
		$lesson->add_to_dropbox();
	}
	
	$exportable_lessons = count(Lesson::find_all_exportable_lessons());
	$queued_lessons = count(Lesson::find_all_queued_lessons());
	
	$moveable_lessons = count(Lesson::find_all_moveable_lessons());
	$language_checked_lessons = count(Lesson::find_all_ready_to_video_check_lessons());
	
	if(isset($_GET['sort'])) {
		$sort_by = $db->escape_value($_GET['sort']);
	} else {
		$sort_by = "export";
	}
	
	$qa_lessons = Lesson::find_all_checkable_lessons($sort_by);
?>

<?php include_layout_template('header.php'); ?>

	<div id="breadcrumbs" class="row">
		<ul class="breadcrumbs">
			<li class="current"><a href="operations.php">Operations</a></li>
		</ul>
	</div>

  <?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>

	<div id="page-header" class="row">
		<div class="small-12 columns">
			<h3>Operations</h3>
		</div>
	</div>
	<div id="panels" class="row">	
		<div id="export-queue" class="panel small-12 medium-6 columns">
			<p>Ready to Render: <strong><?php echo $exportable_lessons; ?></strong></p>
			<p>Currently in Queue: <strong><?php echo $queued_lessons; ?></strong></p>
			<p><a href="render-queue.php">Go to Render Page</a></p>
		</div>
		<div id="video-checking" class="panel small-12 medium-6 columns">
			<p>Files to Archive: <strong><?php echo $moveable_lessons; ?></strong></p>
			<p>Ready to Check: <strong><?php echo $language_checked_lessons; ?></strong></p>
			<p><a href="admin-video-check.php">Go to Video Check Page</a></p>
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
    				    <form action='operations.php' method='post'>
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
							<input type='hidden' name='qa_lesson_id' value='<?php echo $lesson->id; ?>'>
              <input type="submit" value="Update QA" class="no-format" name="changed_qa" data-tooltip class="has-tip" title="Update QA Log">
  						</form>
  					</li>
  					<li class="action-item">
  					  <form action='operations.php' method='post'>
                <input type='hidden' name='qa_lesson_id' value='<?php echo $lesson->id; ?>'>
                <input type='submit' class="no-format" name='marked_lesson_language_checked' value='Mark Language Checked' data-tooltip class="has-tip" title="Mark as Language Checked"></form>
  					</li>
    			</div>
        </div>
      <?php endforeach; ?>
      </ol>
      <?php } else { ?>
        <div class="group-item">
          <div class="lesson-info">
            <a class="lesson-title" href="#">No lessons waiting on language check.</a>
          </div>
        </div>
      <?php } ?>
    </div>
	</div>
	
	
	
	
		
<?php include_layout_template('footer.php'); ?>