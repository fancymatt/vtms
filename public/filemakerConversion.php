<?php require_once("../includes/initialize.php"); ?>
<?php

// Script to add missing assets after importing tasks from FileMaker

// Add Assets
// Listening


/*

$listening_lessons = Lesson::find_all_lessons_for_series(10);
foreach($listening_lessons as $lesson) {
	// Make new asset tasks, 51 - 52
	$task1 = new Task();
	$task1->team_member_id = 1;
	$task1->lesson_id = $lesson->id;
	$task1->global_task_id = 51;
	$task1->is_completed = 1;
	$task1->is_delivered = 1;
	$task1->create();
	
	$task2 = new Task();
	$task2->team_member_id = 1;
	$task2->lesson_id = $lesson->id;
	$task2->global_task_id = 52;
	$task2->is_completed = 1;
	$task2->is_delivered = 1;
	$task2->create();
}



// Holidays

$holiday_lessons = Lesson::find_all_lessons_for_series(3);
foreach($holiday_lessons as $lesson) {
	// Make new asset tasks
	// 48 - Make Title Slide
	// 49 - Make Thumbnails
	// 50 - Make Images
	$slideTask = new Task();
	$slideTask->team_member_id = 21;
	$slideTask->lesson_id = $lesson->id;
	$slideTask->global_task_id = 48;
	$slideTask->is_completed = 1;
	$slideTask->is_delivered = 1;
	$slideTask->create();
	
	$thumbTask = new Task();
	$thumbTask->team_member_id = 21;
	$thumbTask->lesson_id = $lesson->id;
	$thumbTask->global_task_id = 49;
	$thumbTask->is_completed = 1;
	$thumbTask->is_delivered = 1;
	$thumbTask->create();
	
	$imagesTask = new Task();
	$imagesTask->team_member_id = 21;
	$imagesTask->lesson_id = $lesson->id;
	$imagesTask->global_task_id = 50;
	$imagesTask->is_completed = 1;
	$imagesTask->is_delivered = 1;
	$imagesTask->create();
}
echo "Added Holiday assets<br />";


// Pronunciation
// Add missing articulation tasks


$pronunciation_lesson_ids = array('2178', '2179', '2180', '2188', '2189', '2190');
foreach($pronunciation_lesson_ids as $id) {
	$articulation = new Task();
	$articulation->team_member_id = 10;
	$articulation->lesson_id = $id;
	$articulation->global_task_id = 26;
	$articulation->is_completed = 1;
	$articulation->is_delivered = 1;
	$articulation->create();
}


// Writing
// Add missing Shoot Handwriting tasks

$writing_lessons = Lesson::find_all_lessons_for_series(2);
foreach($writing_lessons as $lesson) {
	// New asset tasks
	// ID - 28
	$handwritingTask = new Task();
	$handwritingTask->team_member_id = 3;
	$handwritingTask->lesson_id = $lesson->id;
	$handwritingTask->global_task_id = 28;
	$handwritingTask->is_completed = 1;
	$handwritingTask->is_delivered = 1;
	$handwritingTask->create();
}



$pronunciation_lessons = Lesson::find_all_lessons_for_series(12);
foreach($pronunciation_lessons as $lesson) {
	// Make new asset tasks
	// 89 - Shoot Target MC
	
	$task = new Task();
	$task->team_member_id = 1;
	$task->lesson_id = $lesson->id;
	$task->global_task_id = 89;
	$task->is_completed = 1;
	$task->is_delivered = 1;
	$task->create();
}
*/


echo "Done!";


?>