<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	if(isset($_POST['mark_lesson_as_checked'])) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->checked_video = 1;
		$lesson->update();
		$_SESSION['message'] = $lesson->display_full_lesson_text()." has been video approved.";
		redirect_to('admin-video-check.php');
		
	}
	
	if(isset($_POST['mark_lesson_as_moved'])) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->files_moved = 1;
		$lesson->update();
		$_SESSION['message'] = $lesson->display_full_lesson_text()." has been moved.";
		redirect_to('admin-video-check.php');
	}
	
	$moveable_lessons = Lesson::find_all_moveable_lessons();
	$sort_by = $db->escape_value($_GET['sort']);
	$language_checked_lessons = Lesson::find_all_ready_to_video_check_lessons($sort_by);
?>

<?php include_layout_template('header.php'); ?>

  <?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	
	<div id="breadcrumbs" class="row">
		<ul class="breadcrumbs">
			<li><a href="operations.php">Operations</a></li>
			<li class="current"><a href="admin-video-check.php">Video Checking</a></li>
		</ul>
	</div>
	
	<div id="content" class="row">
		<div class="medium-6 small-12 columns">
			<h3 class="lesson-group-heading">Check These</h3>
			<ol class="lesson-group">
			<?php
			if(!$language_checked_lessons) {
				echo "<tr><td>No lessons</td><tr>";
			} else {
				foreach($language_checked_lessons as $qa_lesson) { ?>
					<li class="lesson">
						<p class="lesson-title"><?php echo $qa_lesson->display_full_lesson(); ?></p>
						<p class="lesson-due-date">Due <?php echo date("F j, Y", strtotime($qa_lesson->publish_date)); ?></p>
						<ul class="lesson-actions">
							<li class="lesson-action-item">
								<a class="item" href="issues-for-lesson.php?id=<?php echo $qa_lesson->id; ?>" data-tooltip class="has-tip" title="Add Issue"><img src="img/icon-add-issue.png"></a>
							</li>
							<li class="lesson-action-item">
								<form action='admin-video-check.php' method='post'>
									<input type='hidden' name='qa_lesson_id' value='<?php echo $qa_lesson->id; ?>'>
                  <button type="submit" class="no-format" name="mark_lesson_as_checked" data-tooltip class="has-tip" title="Mark Lesson as Checked"><img src="img/icon-move-files.png"></button>
								</form>
							</li>
						</ul>	
					</li>				
				<?php } ?>
		<?php } ?>
			</ol>
		</div>
		<div class="medium-6 small-12 columns">
			<h3 class="lesson-group-heading">Move These</h3>
			<ol class="lesson-group">
				<?php 
				if(!$moveable_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($moveable_lessons as $qa_lesson): ?>
						<li class="lesson">
							<p class="lesson-title"><?php echo $qa_lesson->display_full_lesson(); ?></p>
							<p class="lesson-due-date">Due <?php echo date("F j, Y", strtotime($qa_lesson->publish_date)); ?></p>
							<ul class="lesson-actions">
								<li class="lesson-action-item">
									<form action='admin-video-check.php' method='post'>
										<input type='hidden' name='qa_lesson_id' value='<?php echo $qa_lesson->id; ?>'>
										<button type="submit" class="no-format" name="mark_lesson_as_moved" data-tooltip class="has-tip" title="Move Files"><img src="img/icon-move-files.png"></button>									</form>
								</li>
							</ul>
						</li>
					<?php endforeach; ?>
				<?php } ?>
			</ol>
		</div>
  </div>
		
<?php include_layout_template('footer.php'); ?>