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
	
	$upload_site_lessons = count(Lesson::get_ready_to_publish_lessons());
	$upload_youtube_lessons = count(Lesson::get_ready_to_publish_youtube_lessons());
	$upload_illtv_lessons = count(Lesson::find_all_lessons_that_need_upload_to_ill_tv());
	$test_illtv_lessons = count(Lesson::find_all_lessons_that_need_testing_on_ill_tv());
	
	$need_trt_lessons = count(Lesson::find_all_recently_completed_lessons("order"));

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
  			<p><a href="render-queue.php">Detail</a></p>
  		</div>
  		<div id="video-checking" class="panel small-12 medium-6 columns">
  			<p>Files to Archive: <strong><?php echo $moveable_lessons; ?></strong></p>
  			<p>Ready to Check: <strong><?php echo $language_checked_lessons; ?></strong></p>
  			<p><a href="admin-video-check.php">Detail</a></p>
  		</div>
    </div>
  </div>
  
  <div class="row">
    <div class="small-12 medium-8 medium-centered columns">
      <div id="upload-sites" class="panel small-12 medium-6 columns">
  			<p>Ready to Upload to the Sites: <strong><?php echo $upload_site_lessons; ?></strong></p>
  			<p><a href="ready-to-publish.php">Detail</a></p>
  		</div>
  		<div id="upload-yt" class="panel small-12 medium-6 columns">
  			<p>Ready to Upload to YouTube: <strong><?php echo $upload_youtube_lessons; ?></strong></p>
  			<p><a href="ready-to-publish-yt.php">Detail</a></p>
  		</div>
    </div>
  </div>
  
  <div class="row">
    <div class="small-12 medium-8 medium-centered columns">
  		<div id="upload-illtv" class="panel small-12 medium-6 columns">
  			<p>Ready to Upload to ILL TV: <strong><?php echo $upload_illtv_lessons; ?></strong></p>
  			<p><a href="ready-to-upload-ill-tv.php">Detail</a></p>
  		</div>
      <div id="test-illtv" class="panel small-12 medium-6 columns">
  			<p>Ready to Test on ILL TV: <strong><?php echo $test_illtv_lessons; ?></strong></p>
  			<p><a href="ready-to-test-ill-tv.php">Detail</a></p>
  		</div>
    </div>
  </div>
	
	<div class="row">
    <div class="small-12 medium-8 medium-centered columns">
  		<div id="upload-illtv" class="panel small-12 medium-6 columns">
  			<p>Lessons needing TRT: <strong><?php echo $need_trt_lessons; ?></strong></p>
  			<p><a href="publishing.php">Detail</a></p>
  		</div>
    </div>
  </div>
	
<?php include_layout_template('footer.php'); ?>