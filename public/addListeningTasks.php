<?php require_once("../includes/initialize.php"); ?>
<?php
	$session->confirm_logged_in();
	$series_id = 10;
	$listening_lessons = Lesson::find_all_lessons_for_series(10);
	
	foreach ($listening_lessons as $lesson) {
		$new_male_task = new Task();
		$new_male_task->lesson_id = $lesson->id;
		$new_male_task->global_task_id = 51;
		$new_male_task->is_completed = 1;
		$new_male_task->create();
		
		$new_female_task = new Task();
		$new_female_task->lesson_id = $lesson->id;
		$new_female_task->global_task_id = 52;
		$new_female_task->is_completed = 1;
		$new_female_task->create();
	}
	redirect_to("viewSeries.php?id={$series_id}");
	// Small change for git
	
?>

<?php include_layout_template('header.php'); ?>
	<div>
		Okay!
	</div>
<?php include_layout_template('footer.php'); ?>