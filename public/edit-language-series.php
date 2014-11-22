<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	$session->confirm_logged_in();
	
	$current_language_series_id = $_GET['id'];
	$current_language_series = LanguageSeries::find_by_id($current_language_series_id);
	if (!$current_language_series->series_name) {
		redirect_to("lesson-db.php");
	}	
	$current_series = Series::find_by_id($current_language_series->series_id);
	
	$languages = Language::find_all();
	$levels = Level::find_all();
	$channels = Channel::find_all();
	$talents = Talent::find_all();
	
  if($_POST['edited_language_series']) {
		$edited_language_series_id = $db->escape_value($_POST['edited_language_series_id']);
		$edited_language_series_name = $db->escape_value($_POST['edited_language_series_name']);
		$edited_language_series_language_id = $db->escape_value($_POST['edited_language_series_language_id']);
		$edited_language_series_level_id = $db->escape_value($_POST['edited_language_series_level_id']);
		$edited_language_series_channel_id = $db->escape_value($_POST['edited_language_series_channel_id']);
		$edited_language_series_talent_id = $db->escape_value($_POST['edited_language_series_talent_id']);
		
		$required_fields = array(edited_language_series_name);
		validate_presences($required_fields);
		
		$edited_language_series = LanguageSeries::find_by_id($edited_language_series_id);
		
		$edited_language_series->language_id = $edited_language_series_language_id;
		$edited_language_series->language_series_title = $edited_language_series_name;
		$edited_language_series->level_id = $edited_language_series_level_id;
		$edited_language_series->channel_id = $edited_language_series_channel_id;
		$edited_language_series->talent_id = $edited_language_series_talent_id;
		$edited_language_series->update();
		redirect_to("edit-language-series.php?id={$edited_language_series->id}");	
		
		if(!empty($session->errors)) {
			$_SESSION["errors"] = $errors;
		}
	}
	
	if($_POST['deleted_language_series']) {
		$deleted_language_series_id = $database->escape_value($_POST['deleted_language_series_id']);
		$deleted_language_series = LanguageSeries::find_by_id($deleted_language_series_id);
		// First, find all the tasks and delete them
		$deleted_language_series_lessons = Lesson::find_all_child_for_parent($deleted_language_series_id, "lesson", "LanguageSeries", "");
		foreach($deleted_language_series_lessons as $lesson) {
			$deleted_language_series_lesson_id = $lesson->id;
			$deleted_language_series_tasks = Task::find_all_child_for_parent($deleted_language_series_lesson_id, "task", "Lesson", "");
			foreach($deleted_language_series_tasks as $task) {
				$task->delete();
			}
			$lesson->delete();
		}
		$deleted_language_series->delete();
		redirect_to("lesson-db.php");
	}
	
?>

<?php include_layout_template('header.php'); ?>

<?php if($message) { ?>
<div data-alert class="alert-box">
  <?php echo $message; ?>
  <a href="#" class="close">&times;</a>
</div>
<?php } ?>
  
<div class="small-12 medium-8 medium-centered columns">
  <div id="breadcrumbs" class="row">
  	<ul class="breadcrumbs">
  		<li><a href="lesson-db.php">Lesson DB</a></li>
  		<li><a href="series.php?id=<?php echo $current_series->id; ?>"><?php echo $current_series->title; ?></a></li>
  		<li><a href="language-series.php?id=<?php echo $current_language_series_id; ?>"><?php echo $current_language_series->language_series_title; ?></a></li>
  		<li class="current">
  			<a href="#">
  				Edit
  			</a>
  		</li> 
  	</ul>
  </div>

	<div class="panel">
		<h4>Edit: <?php echo $current_language_series->language_series_title; ?></h4>
		<p><?php echo "({$current_language_series->language_name} {$current_language_series->series_name})"; ?></p>
		<form action="edit-language-series.php?id=<?php echo $current_language_series_id; ?>" method="POST">
  		
			<label for="edited_langauge_series_name">Series Name: </label>
			 <input type="text" size="80" name="edited_language_series_name" value="<?php echo $current_language_series->language_series_title; ?>">
			
			<label for="edited_language_series_language_id">Language: </label>
			<select name="edited_language_series_language_id" id="edited_language_series_language_id">
  			<option value="0">None</option>
				<?php 
  				foreach($languages as $language) {
					echo "<option value='{$language->id}'";
					if ($language->name == $current_language_series->language_name) {
						echo " selected";
					}
					echo ">{$language->name}</option>";
				} 
				?>
			</select>
			
			<label for="edited_language_series_level_id">Level: </label>
			<select name="edited_language_series_level_id" id="edited_language_series_level_id">
  			<option value="0">None</option>
				<?php 
  				foreach($levels as $level) {
					echo "<option value='{$level->id}'";
					if ($level->id == $current_language_series->level_id) {
						echo " selected";
					}
					echo ">{$level->name}</option>";
				} 
				?>
			</select>
			
			<label for="edited_language_series_channel_id">Channel: </label>
			<select name="edited_language_series_channel_id">
  			<option value="0">None</option>
  			<?php 
    			foreach($channels as $channel) {
    			echo "<option value='{$channel->id}'";
    			if ($channel->id == $current_language_series->channel_id) {
      			echo " selected";
    			}
    			echo ">{$channel->name}</option>";
  			}
  			?>
			</select>
			
			<label for="edited_language_series_talent_id">Talent: </label>
			<select name="edited_language_series_talent_id">
  			<option value="0">None</option>
  			<?php 
    			foreach($talents as $talent) {
    			echo "<option value='{$talent->id}'";
    			if ($talent->id == $current_language_series->talent_id) {
      			echo " selected";
    			}
    			echo ">";
    			echo $talent->name_first . " " . $talent->name_last;
    			echo "</option>";
  			}
  			?>
			</select>
			
			<input type="hidden" name="edited_language_series_id" value="<?php echo $current_language_series->id; ?>">
			<p><input type="submit" name="edited_language_series" id="edited_language_series" class="action button"></p>
		</form>
	</div>
	<form action="edit-language-series.php?id=<?php echo $current_language_series_id; ?>" method="POST">
		<input type="hidden" name="deleted_language_series_id" id="deleted_language_series_id" value="<?php echo $current_language_series->id; ?>">
		<input type="submit" name="deleted_language_series" id="deleted_language_series" class="button alert" value="Delete Language Series" onclick="return confirm('Are you sure you want to delete this?')">
	</form>
<?php include_layout_template('footer.php'); ?>