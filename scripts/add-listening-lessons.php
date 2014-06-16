<?php require_once("../includes/initialize.php"); ?>
<?php

$session->confirm_logged_in();
	
	// Language Series which need the lessons applied to them

/*
	//$language_series_abs_ids = array(90, 91, 92, 93, 94, 75, 64, 66, 95, 96, 97, 80, 72, 74, 98, 99, 100, 101, 102);
	$language_series_beg_ids = array(79, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 134);
	$language_series_int_ids = array(152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183);
	$language_series_adv_ids = array(199, 200, 201, 202, 203, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215, 216, 217, 218, 219, 220, 221, 222, 223, 224, 225, 226, 227, 228, 229, 230);
	$unit_titles = array("6" => "Deciding on a Hotel in <country>", 
						 "7" => "Setting up a Meeting Room in <country>",
						 "8" => "Ordering Office Supplies in <language>", 
						 "9" => "Getting to the Airport in <country>", 
						 "10" => "Talking to a Supplier in <language>");
	$due_dates = array("2014-12-23", "2015-01-13", "2015-01-27", "2015-02-10", "2015-02-24");
	foreach($language_series_adv_ids as $language_series_id) {
		// Create 5 lessons with the correct names and add them to the series
		// Get language
		$language_series = LanguageSeries::find_by_id($language_series_id);
		$language = Language::find_by_id($language_series->language_id);
		$i=0;
		foreach ($unit_titles as $num => $title) {	
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
	Done!