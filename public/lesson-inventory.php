<?php require_once("../includes/initialize.php"); ?>
<?php
	$languages = Language::find_all();
	
	$total_number = 0;
	$total_time = 0;
	foreach($languages as $key=>$value) {
		if(isset($value->lesson_count)) {
			$total_number += $value->lesson_count;
		}
		if(isset($value->language_trt)) {
			$total_time += $value->language_trt;
		}
	}

?>

<?php include_layout_template('header.php'); ?>
  <div class="row">
    <div class="small-12 columns">
		<h3 class="main_title">Video Lesson Inventory</h3>
    </div>
  </div>
  
  <?php if($message) { ?>
  <div class="row">
  	<div data-alert class="alert-box">
  	  <?php echo $message; ?>
  	  <a href="#" class="close">&times;</a>
  	</div>
  </div>
	<?php } ?>

	<div id="languages">
				<?php
				echo "<p>{$total_number} lessons</p>";
				echo "<p>".$total_time."</p>"; 
				echo "<ul>";
				if(!$languages) {
					echo "<p>No lessons</p>";
				} else {
					foreach($languages as $language) { 
						$language_series = LanguageSeries::find_all_language_series_for_language($language->id);
						if($language->lesson_count > 0) {
							echo "<li><strong>".$language->name." (".$language->lesson_count." lessons - ".seconds_to_timecode($language->language_trt,6).")</strong></li>";
							echo "<ul>";
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
	  
      <p><?php echo $total_number; ?> lessons</p>
			<p><?php echo $total_time; ?></p>
			<ul><?php
				if($languages) {
					foreach($languages as $language) :
						$language_series = LanguageSeries::find_all_language_series_for_language($language->id);
						if($language->lesson_count > 0) { ?>
						  <li><?php echo $language->name." (".$language->lesson_count.") lessons - ".$language->language_trt; ?></li>
						  
							<ul><?php foreach($language_series as $series) :
								$lessons = Lesson::find_all_completed_lessons_for_language_series($series->id);
								if(count($lessons) > 0) { ?>
									<li><?php echo $series->language_series_title." - ".$series->level_name." (".count($lessons)." lessons - ".$series->total_trt.")"; ?></li>
								<?php } ?> <!-- end if(count($lessons)) -->
							</ul>
							<?php endforeach; ?>	
						<?php } ?> <!-- end if($language->lesson_count) -->	
						</ul>
					<?php endforeach; ?>
					
					
					
				<?php } ?> <!-- end if($languages) -->
		</ul>
		</table>
	</div>
		
<?php include_layout_template('footer.php'); ?>