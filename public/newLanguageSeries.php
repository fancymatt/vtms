<?php require_once("../includes/initialize.php"); ?>
<?php
	$session->confirm_logged_in();
	$series_id = $database->escape_value($_GET['inSeries']);
	
	if($_POST['new_language_series']) {
		
		$required_fields = array("new_language_series_name", "new_language_series_language_id", "new_language_series_level_id");
		validate_presences($required_fields);
		
		if(!empty($errors)) {
			$_SESSION["errors"] = $errors;
			redirect_to("viewSeries?id={$series_id}.php");
		}
	
		$new_language_series_name = $database->escape_value($_POST['new_language_series_name']);
		$new_language_series_language_id = $database->escape_value($_POST['new_language_series_language_id']);
		$new_language_series_level_id = $database->escape_value($_POST['new_language_series_level_id']);
		$new_language_series_total_lessons = $database->escape_value($_POST['new_language_series_total_lessons']);
		
		$new_language_series = new LanguageSeries();
		$new_language_series->language_series_title = $new_language_series_name;
		$new_language_series->language_id = $new_language_series_language_id;
		$new_language_series->level_id = $new_language_series_level_id;
		$new_language_series->series_id = $series_id;
		$new_language_series->create();
		$new_language_series_id = $database->insert_id();
		
		// Create all the lessons for the series
		for($i=1; $i<=$new_language_series_total_lessons; $i++) {
			$new_lesson_name = "Lesson ".$i;
			$new_lesson_number = $i;
			$new_lesson_language_series = $new_language_series_id;
			
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
		}
		
		
		redirect_to("viewSeries.php?id={$series_id}");
	}
	
	$levels = Level::find_all();
	$languages = Language::find_all();
?>

<?php include_layout_template('header.php'); ?>
	<div>
		<?php $errors = $session->errors(); ?>
		<?php echo $session->form_errors($errors); ?>
		<h2>Create New Language Series in <?php echo Series::get_series_title_from_id($series_id); ?></h2>
		<form action="newLanguageSeries.php?inSeries=<?php echo $series_id; ?>" method="POST">
			<p><label for="new_langauge_series_name">Full Series Name: </label><input type="text" size="80" name="new_language_series_name"></p>
			<p>Language: <select name="new_language_series_language_id" id="new_language_series_language_id">
				<?php foreach($languages as $language) {
					echo "<option value='{$language->id}'";
					if ($language->name == $current_language_series->language_name) {
						echo " selected";
					}
					echo ">{$language->name}</option>";
				} ?>
			</select></p>
			<p>Level: <select name="new_language_series_level_id" id="new_language_series_level_id">
				<?php foreach($levels as $level) {
					echo "<option value='{$level->id}'";
					echo ">{$level->name}</option>";
				} ?>
			</select></p>
			<p>Total Lessons: <select name="new_language_series_total_lessons" id="new_language_series_total_lessons">
			<?php for($i=0; $i<=50; $i++) {
					echo "<option value='{$i}'";
					echo ">{$i}</option>";
					} ?>
			</select></p>
			<p><input type="submit" name="new_language_series" id="new_language_series"></p>
		</form>
	</div>
	<script src="script.js"></script>
<?php include_layout_template('footer.php'); ?>