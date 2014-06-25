<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	if($_POST['mark_lesson_as_checked']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->checked_video = 1;
		$lesson->update();
	}
	
	if($_POST['move_lesson']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->files_moved = 1;
		$lesson->update();
	}
	
	$moveable_lessons = Lesson::find_all_moveable_lessons();
	$sort_by = $db->escape_value($_GET['sort']);
	$language_checked_lessons = Lesson::find_all_ready_to_video_check_lessons($sort_by);
?>

<?php include_layout_template('header.php'); ?>
	
	<div id="breadcrumbs" class="row">
		<ul class="breadcrumbs">
			<li><a href="operations.php">Operations</a></li>
			<li class="current"><a href="admin-video-check.php">Video Checking</a></li>
		</ul>
	</div>
	
	<?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	
	<div id="videos-to-move" class="row">
		<div class="small-12 columns">
			<h4>Move These</h4>
			<table>
				<tr>
					<th>Lesson</th>
					<th>Actions</th>
					<th>Due Date</th>
				</tr>
				<?php 
				if(!$moveable_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($moveable_lessons as $qa_lesson) {
						echo "<tr>";
						echo "<td>";
						echo $qa_lesson->display_full_lesson();
						echo "</td>";
						echo "<td><form action='admin-video-check.php' method='post'>";
						echo "<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'>"; ?>
						<input type="image" src="img/icon-move-files.png" name='move_files' value='Move Files' data-tooltip class="has-tip" title="Move Files">
						<?php
						echo "</td>";
						echo "<td>{$qa_lesson->publish_date}</td>";
						echo "</tr>";
					} 
				 } ?>		
			</table>
		</div>
	
		<div class="small-12 columns">
			<h4>Check These</h4>
				<ol class="lesson-group">
				<?php
				if(!$language_checked_lessons) {
					echo "<tr><td>No lessons</td><tr>";
				} else {
					foreach($language_checked_lessons as $qa_lesson): ?>
					<li class="lesson-group-item">
						<p class="lesson-title"><?php echo $qa_lesson->display_full_lesson(); ?></p>
						<div class="lesson-meta">
							<p class="lesson-due-date">
								<?php echo $qa_lesson->publish_date; ?>
							</p>
						</div>
						<ul class="lesson-actions">
							<li class="lesson-action-item">
								<a class="item" href="issues-for-lesson.php?id=<?php echo $qa_lesson->id; ?>"><img src="img/icon-add-issue.png"></a>
							</li>
							<li>
								<form action='admin-video-check.php' method='post'>
									<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'>
									<input class="item" type='image' src="img/icon-move-files.png" name='mark_lesson_as_checked' value='Mark as Checked' data-tooltip class="has-tip" title="Mark Lesson as Checked">
								</form>
							</li>
						</ul>						
					</div>
					<?php endforeach; ?>
				<?php } ?>
			</table>
		</div>
	</div>
		
<?php include_layout_template('footer.php'); ?>