<?php require_once("../includes/initialize.php"); ?>
<?php
	$asset_id = $db->escape_value($_GET['id']);
	$asset = Task::find_by_id($asset_id);
	$lesson = Lesson::find_by_id($asset->lesson_id);
	$language_series = LanguageSeries::find_by_id($lesson->language_series_id);
	$series = Series::find_by_id($language_series->series_id);
	$logged_in_user = User::find_by_id($session->user_id);
	$active_shift = Shift::get_active_shift_for_member($logged_in_user->id);
	
	if($_POST['shot_completed']) {
		$shot_id = $_POST['shot_id'];
		$shoot_notes = $_POST['shoot_notes'];
		$completed_shot = Shot::find_by_id($shot_id);
		$completed_shot->is_completed = 1;
		$completed_shot->script_video = $shoot_notes;
		$completed_shot->update();
		
		// If it's not activated, activate it
		if(!$asset->is_active) {
			$asset->activate_task();
			
			if(is_object($active_shift)) {
  		  $activity = new Activity();
    		$activity->shift_id = $active_shift->id;
    		$activity->time_start = $current_time->format('Y-m-d H:i:s');
    		$activity->task_id = $asset_id;
    		$activity->is_active = 1;
    		$activity->create();	
			}
			$message = "This asset has been set to active because you began completing shots. To undo, go to your task sheet";
		}
		
		// If this completes the asset, mark it as complete
		if(count(Shot::find_all_shots_for_asset($asset_id)) <= count(Shot::find_all_completed_shots_for_asset($asset_id))) {
			$asset->complete_task();
			
			$activity = Activity::get_active_activity_for_member($logged_in_user->id);
			if($activity) {
  		  $activity->is_active = 0;
    		$activity->is_completed = 1;
    		$activity->time_end = $current_time->format('Y-m-d H:i:s');
    		$activity->update();
  			
  			$message = "Asset Completed";	
			}
		}
	}
	
	if($_POST['shot_uncompleted']) {
		$shot_id = $_POST['shot_id'];
		$uncompleted_shot = Shot::find_by_id($shot_id);
		$uncompleted_shot->is_completed = 0;
		$uncompleted_shot->update();
		if($asset->is_active && count(Shot::find_all_completed_shots_for_asset($asset_id) < 1)) {
			$asset->is_active = 0;
			$asset->activated_time = 0;
			$asset->update();
			$message = "This asset has been set to inactive because you have undone all of the shots and started from the beginning.";
		} 
	}
	
	$shots = Shot::find_all_shots_for_asset($asset_id);
	$incomplete_shots = Shot::find_all_incomplete_shots_for_asset($asset_id);
?>
<?php include_layout_template('header.php'); ?>

<div id="breadcrumbs" class="row">
	<ul class="breadcrumbs">
		<li><a href="lesson-db.php">Lesson DB</a></li>
		<li><a href="series.php?id=<?php echo $series->id; ?>"><?php echo $series->title; ?></a></li>
		<li>
			<a href="language-series.php?id=<?php echo $language_series->id; ?>">
				<?php echo $language_series->language_series_title." (".$language_series->level_code.")"; ?>
			</a>
		</li> 
		<li>
		  <a href="lesson.php?id=<?php echo $lesson->id; ?>"><?php echo $lesson->number.". ".$lesson->title; ?></a>
		</li>
		<li><a href="lesson-script.php?id=<?php echo $lesson->id; ?>">Script</a></li>
		<li class="current">
			<a href="#"><?php echo $asset->task_name; ?></a>
		</li>
	</ul>
</div>

	<?php if($message) {
		echo "<p>{$message}</p>";
	} ?>

	<div id="teleprompter_script" class="row">
		<textarea rows="10" cols="100">
		<?php if($incomplete_shots) {
			echo "\n\n";
			echo "{$lesson->language_name} {$lesson->series_name} {$lesson->number} ($asset->task_name)";
			echo "\n\n";
			foreach($incomplete_shots as $shot) {
				echo "[{$lesson->language_name} {$lesson->series_name} #{$lesson->number}-{$shot->shot}-{$shot->type}]";
				echo "\n";
				echo $shot->script;
				echo "\n\n";
			}
		} else {
			echo "This asset has been completely recorded.";
		} ?>
		</textarea>
	</div>
	<div id="list" class="row">
	  <div class="small-12 columns">
    	<table class="script">
    		<tr><th>Shot</th><th>Script</th><th>Script English</th><th>Recording Comments</th><th>Actions</th></tr>
    		<?php foreach($shots as $shot): ?>
				<tr<?php if($shot->is_completed) { echo " class='completed'"; } ?>> 
					<td>
						<?php echo "{$shot->section} {$shot->shot} - {$shot->type}"; ?>
					</td>
					<td>
						<?php echo nl2br($shot->script); ?>
					</td>
					<td>
						<?php echo nl2br($shot->script_english); ?>
					</td>
					<td>
						<form method="post" action="record-asset.php?id=<?php echo $asset_id; ?>">
						<input type="hidden" name="shot_id" value="<?php echo $shot->id; ?>">
						<textarea name="shoot_notes" rows=5 cols=20><?php echo $shot->script_video; ?></textarea>
						<input type="submit" name="shot_completed" value="<?php echo ($shot->is_completed ? "Update Log" :  "Mark as Complete") ?>"></form>
					</td>
					<td>
						<?php if($shot->is_completed) { ?>
						<form method="post" action="record-asset.php?id=<?php echo $asset_id; ?>">
						<input type="hidden" name="shot_id" value="<?php echo $shot->id; ?>">
						<input type="submit" name="shot_uncompleted" value="Mark as Incomplete"></form>	
						<?php } ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</table>
	  </div>
	</div>
</div>

<?php include_layout_template('footer.php'); ?>