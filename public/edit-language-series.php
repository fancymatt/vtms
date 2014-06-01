<?php require_once("../includes/initialize.php"); ?>
<?php
	$session->confirm_logged_in();
	
	if($_POST['edited_language_series']) {
		$edited_language_series_id = $db->escape_value($_POST['edited_language_series_id']);
		$edited_language_series_name = $db->escape_value($_POST['edited_language_series_name']);
		$edited_language_series_language_id = $db->escape_value($_POST['edited_language_series_language_id']);
		$edited_language_series_level_id = $db->escape_value($_POST['edited_language_series_level_id']);
		
		$required_fields = array(edited_language_series_name);
		validate_presences($required_fields);
		
		$edited_language_series = LanguageSeries::find_by_id($edited_language_series_id);
		
		$edited_language_series->language_id = $edited_language_series_language_id;
		$edited_language_series->language_series_title = $edited_language_series_name;
		$edited_language_series->level_id = $edited_language_series_level_id;
		$edited_language_series->update();
		redirect_to("language-series.php?series={$edited_language_series->series_id}&id={$edited_language_series_id}");	
		
		if(!empty($session->errors)) {
			$_SESSION["errors"] = $errors;
		}
	}
	
	if($_POST['deleted_language_series']) {
		$deleted_language_series_id = $database->escape_value($_POST['deleted_language_series_id']);
		$deleted_language_series = LanguageSeries::find_by_id($deleted_language_series_id);
		// First, find all the tasks and delete them
		$deleted_language_series_lessons = Lesson::find_all_child_for_parent($deleted_language_series_id, "lesson", "LanguageSeries", "");
		foreach($deleted_language_series_lessons as $lesson) {
			$deleted_language_series_lesson_id = $lesson->id;
			$deleted_language_series_tasks = Task::find_all_child_for_parent($deleted_language_series_lesson_id, "task", "Lesson", "");
			foreach($deleted_language_series_tasks as $task) {
				$task->delete();
			}
			$lesson->delete();
		}
		$deleted_language_series->delete();
		redirect_to("lesson-db.php");
	}
	
	$current_language_series_id = $_GET['id'];
	$current_language_series = LanguageSeries::find_by_id($current_language_series_id);
	if (!$current_language_series->series_name) {
		redirect_to("lesson-db.php");
	}	
	$languages = Language::find_all();
	$levels = Level::find_all();
	
?>

<?php include_layout_template('header.php'); ?>
	<div>
		<?php $errors = $session->errors(); ?>
		<?php echo $session->form_errors($errors); ?>
		<h2>Edit: <?php echo $current_language_series->language_series_title; ?></h2>
		<p><?php echo "({$current_language_series->language_name} {$current_language_series->series_name})"; ?></p>
		<form action="edit-language-series.php?id=<?php echo $current_language_series_id; ?>" method="POST">
			<p><label for="edited_langauge_series_name">Series Name: </label><input type="text" size="80" name="edited_language_series_name" value="<?php echo $current_language_series->language_series_title; ?>"></p>
			<p>Language: <select name="edited_language_series_language_id" id="edited_language_series_language_id">
				<?php foreach($languages as $language) {
					echo "<option value='{$language->id}'";
					if ($language->name == $current_language_series->language_name) {
						echo " selected";
					}
					echo ">{$language->name}</option>";
				} ?>
			</select></p>
			<p>Level: <select name="edited_language_series_level_id" id="edited_language_series_level_id">
				<?php foreach($levels as $level) {
					echo "<option value='{$level->id}'";
					if ($level->id == $current_language_series->level_id) {
						echo " selected";
					}
					echo ">{$level->name}</option>";
				} ?>
			</select></p>
			<input type="hidden" name="edited_language_series_id" value="<?php echo $current_language_series->id; ?>">
			<p><input type="submit" name="edited_language_series" id="edited_language_series"></p>
		</form>
	</div>
	<form action="edit-language-series.php?id=<?php echo $current_language_series_id; ?>" method="POST">
		<input type="hidden" name="deleted_language_series_id" id="deleted_language_series_id" value="<?php echo $current_language_series->id; ?>">
		<input type="submit" name="deleted_language_series" id="deleted_language_series" value="Delete Language Series" onclick="return confirm('Are you sure you want to delete this?')">
	</form>
<?php include_layout_template('footer.php'); ?>