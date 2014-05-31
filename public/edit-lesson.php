<?php require_once("../includes/initialize.php"); ?>
<?php
	$session->confirm_logged_in();
	
	if($_POST['edited_lesson']) { // Record has been edited
		$edited_lesson_id = $database->escape_value($_POST['edited_lesson_id']);
		$edited_lesson = Lesson::find_by_id($edited_lesson_id);
		$edited_lesson->title = $database->escape_value($_POST['edited_lesson_title']);
		$edited_lesson->trt = ($database->escape_value($_POST['edited_lesson_trt_minutes']) * 60) + $database->escape_value($_POST['edited_lesson_trt_seconds']);
		$edited_lesson->checked_language = $database->escape_value($_POST['edited_lesson_checked_language']);
		$edited_lesson->checked_video = $database->escape_value($_POST['edited_lesson_checked_video']);
		$edited_lesson->files_moved = $database->escape_value($_POST['edited_lesson_files_moved']);
		$edited_lesson->publish_date = $database->escape_value($_POST['edited_lesson_publish_date']);
		$edited_lesson->update();
		redirect_to("admin-language-series.php?series={$edited_lesson->series_id}&id={$edited_lesson->language_series_id}");
		if(!empty($session->errors)) {
			$_SESSION["errors"] = $errors;
		}
	} else if($_POST['deleted_record']) { // Record has been deleted
		$deleted_record_id = $database->escape_value($_POST['deleted_record_id']);
		$deleted_record = Lesson::find_by_id($deleted_record_id);
		$deleted_record_tasks = Task::find_all_child_for_parent($deleted_record_id, "task", "Lesson", "");
			foreach($deleted_record_tasks as $task) {
				$task->delete();
			}
		$deleted_record->delete();
		redirect_to("admin-language-series.php?series={$deleted_record->series_id}&id={$deleted_record->language_series_id}");
	} else { // Page is displaying for first time
		$current_record_id = $_GET['id'];
		$current_record = Lesson::find_by_id($current_record_id);
		$trt = $current_record->trt;
		$trt_minutes = (int) ($trt/60);
		$trt_seconds = $trt%60;
		if (!$current_record->title) {
			redirect_to("admin-language-series.php?series={$current_record->series_id}&id={$current_record->language_series_id}.php");
		}	
	}
?>

<?php include_layout_template('header.php'); ?>
	<div>
		<?php $errors = $session->errors(); ?>
		<?php echo $session->form_errors($errors); ?>
		<h2>Edit Record: <?php echo $current_record->title; ?></h2>
		<form action="edit-lesson.php?id=<?php echo $current_record_id; ?>" method="POST">
			<p><label for="edited_lesson_title">Title:</label> <input type="text" size="50" name="edited_lesson_title" value="<?php echo $current_record->title; ?>"></p>
			<p><label for="edited_lesson_publish_date">Publish Date:</label> <input type="text" size="50" name="edited_lesson_publish_date" value="<?php echo $current_record->publish_date; ?>"></p>
			<p><label for="edited_lesson_trt_minutes">TRT:</label> 
			<select name="edited_lesson_trt_minutes" id="edited_lesson_trt_minutes">
				<?php for($i=0; $i<20; $i++) {
					echo "<option value='{$i}'";
					if ($i == $trt_minutes) {
						echo " selected";
					}
					echo ">{$i}</option>";
				} ?>
			</select>:
			<select name="edited_lesson_trt_seconds" id="edited_lesson_trt_seconds">
				<?php for($i=0; $i<60; $i++) {
					echo "<option value='{$i}'";
					if ($i == $trt_seconds) {
						echo " selected";
					}
					echo ">{$i}</option>";
				} ?>
			</select><br /><br />
			Language Checked<input type="checkbox" name="edited_lesson_checked_language" value="1" <?php echo $current_record->checked_language ? "checked" : ""; ?>><br />
			Video Checked<input type="checkbox" name="edited_lesson_checked_video" value="1" <?php echo $current_record->checked_video ? "checked" : ""; ?>><br />
			Files Moved<input type="checkbox" name="edited_lesson_files_moved" value="1" <?php echo $current_record->files_moved ? "checked" : ""; ?>>
			<input type="hidden" name="edited_lesson_id" value="<?php echo $current_record->id; ?>">
			<p><input type="submit" name="edited_lesson" id="edited_lesson"></p>
		</form>
	</div>
	<form action="edit-lesson.php?id=<?php echo $current_record_id; ?>" method="POST">
		<input type="hidden" name="deleted_record_id" value="<?php echo $current_record->id; ?>">
		<input type="submit" name="deleted_record" id="deleted_record" value="Delete Record" onclick="return confirm('Are you sure you want to delete this?')">
	</form>
<?php include_layout_template('footer.php'); ?>