<?php require_once("../includes/initialize.php"); ?>
<?php

$session->confirm_logged_in();
	
	// Language Series which need the lessons applied to them
/*
	$language_series_abs_ids = array(90, 91, 92, 93, 94, 75, 64, 66, 95, 96, 97, 80, 72, 74, 98, 99, 100, 101, 102);
	$abs_unit2_titles = array("6" => "Seeing a Movie in <country>", 
							  "7" => "Shopping for a Shirt in <country>",
							  "8" => "Ordering a Burger in <language>", 
							  "9" => "Baking a Cake in <country>", 
							  "10" => "Making Plans for the Day in <language>");
	$due_dates = array("2014-05-27", "2014-06-03", "2014-06-10", "2014-06-17", "2014-06-24");
	foreach($language_series_abs_ids as $language_series_id) {
		// Create 5 lessons with the correct names and add them to the series
		// Get language
		$language_series = LanguageSeries::find_by_id($language_series_id);
		$language = Language::find_by_id($language_series->language_id);
		$i=0;
		foreach ($abs_unit2_titles as $num => $title) {	
			$new_lesson_name_pass1 = str_replace("<language>", $language->name, $title);
			$new_lesson_name_pass2 = str_replace("<country>", $language->country_name, $new_lesson_name_pass1);
			$new_lesson_name = $new_lesson_name_pass2;
		
			$lesson = new Lesson();
			$lesson->language_series_id = $language_series_id;
			$lesson->number = $num;
			$lesson->title = $new_lesson_name;
			$lesson->publish_date = $due_dates[$i];
			$lesson->create();
			
			// Create all the tasks for the lesson
			$series_id = $language_series->series_id;
			$global_tasks = GlobalTask::get_all_global_assets_and_tasks_for_series($series_id);
			foreach ($global_tasks as $global_task) {
				// Create tasks
				$new_task = new Task();
				$new_task->global_task_id = $global_task->id;
				$new_task->lesson_id = $lesson->id;
				$new_task->team_member_id = $global_task->default_team_member_id;
				$new_task->create();
			}
			$i++;
		}
	}
*/
?>

<?php include_layout_template('header.php'); ?>
	Done!
<?php include_layout_template('footer.php'); ?>