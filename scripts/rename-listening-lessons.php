<?php require_once("../includes/initialize.php"); ?>
<?php

$session->confirm_logged_in();
	
	/*
$sql = "WHERE series.title='Listening' AND level.code='int' AND lesson.number = 10 ";
	
	$lessons = Lesson::find_all_where($sql);
	foreach($lessons as $lesson) {
		$language_series_id = $lesson->language_series_id;
		$language_series = LanguageSeries::find_by_id($language_series_id);
		$language_id = $language_series->language_id;
		$language = Language::find_by_id($language_id);
		
		$title = 'Deciding When to Move in <country>';
		
		$new_lesson_name_pass1 = str_replace("<language>", $language->name, $title);
		$new_lesson_name_pass2 = str_replace("<country>", $language->country_name, $new_lesson_name_pass1);
		$new_lesson_name = $new_lesson_name_pass2;
		$lesson->title = $new_lesson_name;
		$lesson->update();
	}
*/

?>
	Done!