<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	
	if(isset($_POST['lesson_uploaded'])) {
		$changed_records = 0;
		foreach($_POST['lesson_uploaded'] as $key => $uploaded) {
			$lesson = Lesson::find_by_id($_POST['lesson_id'][$key]);
			$lesson->is_uploaded_for_ill_tv = $uploaded;
			$lesson->update();
			$changed_records++;
		}
		$message = $changed_records . " lessons have been marked as uploaded.";
	}
	
	if(isset($_POST['lesson_checked'])) {
		$changed_records = 0;
		foreach($_POST['lesson_checked'] as $key => $checked) {
			$lesson = Lesson::find_by_id($_POST['lesson_id'][$key]);
			$lesson->is_ill_tv_tested = $checked;
			$lesson->update();
			$changed_records++;
		}
		$message = $changed_records . " lessons have been marked as checked.";
	}
	
	$ill_tv_uploadable_lessons = Lesson::find_all_lessons_ready_to_upload_to_ill_tv();
	$ill_tv_addable_lessons = Lesson::find_all_lessons_ready_to_add_to_ill_tv();
?>

<?php include_layout_template('header.php'); ?>
	<div id="page-header" class="row">
		<div class="small-12 columns">
			<h3>ILL TV</h3>
		</div>
	</div>
	
	<?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	<div id="ready-to-upload" class="row">
		<div class="small-6 columns">
			<h4>Ready to Upload</h4>
			<form action="ill-tv.php" method="post">
			<table>
				<tr><th>Lesson</th><th width="100">Uploaded?</th></tr>
				<?php 
				if(!$ill_tv_uploadable_lessons) {
					echo "<td>No lessons</td>";
				} else {
					$i = 0;
					foreach($ill_tv_uploadable_lessons as $lesson) { ?>
						<tr>
							<td>
								<?php echo $lesson->display_full_lesson(); ?>
							</td>
							<td>
								<input type="checkbox" name="lesson_uploaded[<?php echo $i; ?>]" value="1">
								<input name="lesson_id[<?php echo $i; ?>]" type="hidden" value="<?php echo $lesson->id; ?>">
							</td>
						</tr>
				<?php
					$i++;  
					}
				} ?>		
			</table>
			<input type="submit" name="submit_uploaded_lessons" class="button" value="Uploaded Lessons">
			</form>
		</div>
		<div class="small-6 columns">
			<h4>Ready to Check</h4>
			<form action="ill-tv.php" method="post">
			<table>
				<tr><th>Lesson</th><th width="100">Checked?</th></tr>
				<?php 
				if(!$ill_tv_addable_lessons) {
					echo "<td>No lessons</td>";
				} else {
					$i = 0;
					foreach($ill_tv_addable_lessons as $lesson) { ?>
						<tr>
							<td>
								<?php echo $lesson->display_full_lesson(); ?>
							</td>
							<td>
								<input type="checkbox" name="lesson_checked[<?php echo $i; ?>]" value="1">
								<input name="lesson_id[<?php echo $i; ?>]" type="hidden" value="<?php echo $lesson->id; ?>">
							</td>
						</tr>
				<?php
					$i++;  
					}
				} ?>		
			</table>
			<input type="submit" name="submit_uploaded_lessons" class="button" value="Checked Videos">
			</form>
		</div>
		
<?php include_layout_template('footer.php'); ?>