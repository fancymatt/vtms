<?php require_once("../includes/initialize.php"); ?>
<?php
	$session->confirm_logged_in();
	$language_series_id = $database->escape_value($_GET['inLanguageSeries']);
	
	if($_POST['new_lesson']) {
		
		$required_fields = array("new_lesson_name", "new_lesson_number");
		validate_presences($required_fields);
		
		if(!empty($errors)) {
			$_SESSION["errors"] = $errors;
			redirect_to("viewSeries?id={$series_id}.php");
		}
		$language_series = LanguageSeries::find_by_id($language_series_id);
		$language = Language::find_by_id($language_series->language_id);
		$new_lesson_name = $database->escape_value($_POST['new_lesson_name']);
		$new_lesson_name_pass1 = str_replace("<language>", $language->name, $new_lesson_name);
		$new_lesson_name_pass2 = str_replace("<country>", $language->country_name, $new_lesson_name_pass1);
		$new_lesson_name = $new_lesson_name_pass2;
		$new_lesson_number = $database->escape_value($_POST['new_lesson_number']);
		$new_lesson_language_series = $database->escape_value($_POST['new_lesson_language_series']);
		
		$new_lesson = new Lesson();
		$new_lesson->title = $new_lesson_name;
		$new_lesson->number = $new_lesson_number;
		$new_lesson->language_series_id = $new_lesson_language_series;
		$new_lesson->create();
		$new_lesson_id = $database->insert_id();
	
		// Create all the tasks for the lesson
		$language_series = LanguageSeries::find_by_id($new_lesson_language_series);
		$series_id = $language_series->series_id;
		$global_tasks = GlobalTask::get_all_global_assets_and_tasks_for_series($series_id);
		foreach ($global_tasks as $global_task) {
			// Create tasks
			$new_task = new Task();
			$new_task->global_task_id = $global_task->id;
			$new_task->lesson_id = $new_lesson_id;
			$new_task->team_member_id = $global_task->default_team_member_id;
			$new_task->create();
		}
		
		redirect_to("language-series.php?id={$language_series_id}");
	}
?>

<?php include_layout_template('header.php'); ?>
	<div>
		<?php $errors = $session->errors(); ?>
		<?php echo $session->form_errors($errors); ?>
		<h2>Create New Lesson in <?php echo LanguageSeries::get_language_series_title_from_id($language_series_id); ?></h2>
		<form action="new-lesson.php?inLanguageSeries=<?php echo $language_series_id; ?>" method="POST">
			<p><label for="new_lesson_name">Lesson Name: </label><input type="text" size="80" name="new_lesson_name"></p>
			<p><label for="new_lesson_number">Lesson Number: </label><input type="number" size="5" name="new_lesson_number"></p>
			<input type="hidden" name="new_lesson_language_series" id="new_lesson_language_series" value="<?php echo $language_series_id; ?>">
			<p><input type="submit" name="new_lesson" id="new_lesson"></p>
		</form>
	</div>
	<script src="script.js"></script>
<?php include_layout_template('footer.php'); ?>