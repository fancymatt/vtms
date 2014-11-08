<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	$current_time = new DateTime(null, new DateTimeZone('UTC'));
	$weekly_lessons = Lesson::find_all_lessons_from_last_week($current_time);
	
	$lessons_trt = 0;
	foreach($weekly_lessons as $lesson) {
  	$lessons_trt += $lesson->trt;
	}
?>
<?php include_layout_template('header.php'); ?>
	<div id="page-header" class="row">
		<div class="medium-8 medium-centered small-12 columns">
			<h3>Weekly Lessons</h3>
		</div>
	</div>
	
	<?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	
	<div id="lessons-finished-this-week" class="row">
		<div class="medium-8 medium-centered small-12 columns">
			<table>
				<tr><th>Lesson</th><th>TRT</th><th>Time Moved</th></tr>
				<?php foreach($weekly_lessons as $lesson) { ?>
						<tr>
							<td>
								<?php echo $lesson->display_full_lesson(); ?>
							</td>
							<td>
  							<?php echo gmdate("i:s", $lesson->trt); ?>
  						</td>
  						<td>
    						<?php echo $lesson->files_moved_time; ?>
  						</td>
						</tr>
				<?php } ?>
				<tr>
  				<td>Total:</td>
  				<td><?php echo gmdate("H:i:s", $lessons_trt); ?></td>
          <td></td>
				</tr>
			</table>
		</div>
	</div>
		
<?php include_layout_template('footer.php'); ?>