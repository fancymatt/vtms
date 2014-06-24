<?php require_once("../includes/initialize.php"); ?>
<?php
	$languages = Language::find_all();
?>

<?php include_layout_template('header.php'); ?>
		<h2 class="main_title">Video Lesson Inventory</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
	<div id="languages">
				<?php 
				echo "<ul>";
				if(!$languages) {
					echo "<p>No lessons</p>";
				} else {
					foreach($languages as $language) { 
						if($language->language_trt > 0) {
							echo "<li><strong>".$language->name." (".$language->lesson_count." lessons - ".$language->language_trt.")</strong></li>";
							echo "<ul>";
							$language_series = LanguageSeries::find_all_language_series_for_language($language->id);
							foreach($language_series as $series) {
								$lessons = Lesson::find_all_completed_lessons_for_language_series($series->id);
								if(count($lessons) > 0) {
									echo "<li>".$series->language_series_title." - ".$series->level_name." (".count($lessons)." lessons - ".$series->total_trt.")</li>";
									//foreach($lessons as $lesson) {
										//if($lesson->files_moved) {
											//echo "<li>".$lesson->number.". ".$lesson->title."</li>";
										//}
									}
								//}
							}
							echo "</ul>";	
						}	
					} 
					echo "</ul>";
				 } ?>		
		</table>
	</div>
		
<?php include_layout_template('footer.php'); ?>