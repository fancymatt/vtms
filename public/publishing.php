<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	
	if($_POST["added_lesson_trt"]) {
		$lesson_ids = $_POST['lesson_id'];
		$lesson_trts = $_POST['lesson_trt'];
		$i = 0;
		$changed_records = 0;
		if($lesson_ids) {
			foreach($lesson_ids as $lesson_id) {
				$lesson = Lesson::find_by_id($lesson_id);
				
				if($lesson_trts[$i]) {
					//  & preg_match("^[0-9]+:[0-9]{2}$", $lesson_trts[$i]
					$string_parts = explode (":", $lesson_trts[$i]);
					$mins = $string_parts[0];
					$secs = $string_parts[1];
					$trt = $secs + ($mins*60);
					if(is_int($trt)) {
						$lesson->trt = $trt;
						$lesson->update();
						$changed_records++;
					}
				}
				$i++;
			}
		}
		$message = "TRT for " . $changed_records . " lessons have been updated.";
	}
	
	$recently_completed_lessons = Lesson::find_all_recently_completed_lessons();
?>

<?php include_layout_template('header.php'); ?>
	<div id="page-header" class="row">
		<div class="small-12 columns">
			<h3>Recently Completed Lessons</h3>
		</div>
	</div>
	
	<?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	
	<div id="recently-finished-lessons" class="row">
		<div class="small-12 columns">
			<form action="publishing.php" method="post">
			<table>
				<tr><th>Lesson</th><th width="400">TRT</th></tr>
				<?php 
				if(!$recently_completed_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($recently_completed_lessons as $lesson) { ?>
						<tr>
							<td>
								<?php echo $lesson->display_full_lesson(); ?>
							</td>
							<td>
								<input name="lesson_trt[]" type="text" placeholder="ex. 3:45">
								<input name="lesson_id[]" type="hidden" value="<?php echo $lesson->id; ?>">
							</td>
						</tr>
				<?php }
				} ?>		
			</table>
			<input type="submit" name="added_lesson_trt" class="button">
		</div>
	</div>
		
<?php include_layout_template('footer.php'); ?>