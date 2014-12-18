<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
		
	$exportable_lessons = count(Lesson::find_all_exportable_lessons());
	$queued_lessons = count(Lesson::find_all_queued_lessons());
	
	$moveable_lessons = count(Lesson::find_all_moveable_lessons());
	$language_checked_lessons = count(Lesson::find_all_ready_to_video_check_lessons());	

  
?>

<?php include_layout_template('header.php'); ?>

  <?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	
  <div class="row">
		<div class="small-12 medium-8 medium-centered columns">
  		<ul class="breadcrumbs">
  			<li class="current">Dashboard</li>
  		</ul>
		</div>
  </div>
  
  <div class="row">
    <div class="small-12 medium-8 medium-centered columns">
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
  </div>
	
<?php include_layout_template('footer.php'); ?>