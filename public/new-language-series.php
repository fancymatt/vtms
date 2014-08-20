<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	
	if (isset($_GET['inSeries'])) {
  	$series_id = $database->escape_value($_GET['inSeries']);
  	$series = Series::find_by_id($series_id);
  	
  } else {
    $_SESSION['message'] = "A series ID must be specified in order to add a language series.";
  	redirect_to('lesson-db.php');
  }

	if($_POST['new_language_series']) {
	  $required_fields = array("new_language_series_name", "new_language_series_language_id", "new_language_series_level_id", "new_language_series_total_lessons");
		validate_presences($required_fields);
		
		// TODO Stop creation process if global tasks aren't created
		
		if(!empty($errors)) {
			$_SESSION["message"] = "";
			foreach($errors as $key=>$value) {
  			$_SESSION["message"] .= "{$value}<br />";
  			$_SESSION["new_language_series_name"] = $database->escape_value($_POST['new_language_series_name']);
  			$_SESSION["new_language_series_language_id"] = $database->escape_value($_POST['new_language_series_language_id']);
  			$_SESSION["new_language_series_level_id"] = $database->escape_value($_POST['new_language_series_level_id']);
  			$_SESSION["new_language_series_total_lessons"] = $database->escape_value($_POST['new_language_series_total_lessons']);
  			
			}
			redirect_to("new-language-series.php?inSeries={$series_id}");
		} else {
		  $global_tasks = GlobalTask::get_all_global_assets_and_tasks_for_series($series_id);
		  if (!$global_tasks) {
  		  $_SESSION['message'] = "Global Tasks haven't been set up for this series. If you continue you'll get a bunch of lessons with no tasks, so please set up some Global Tasks and try again.";
    	redirect_to('lesson-db.php');
		  } else {
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
    			foreach ($global_tasks as $global_task) {
    				// Create tasks
    				$new_task = new Task();
    				$new_task->global_task_id = $global_task->id;
    				$new_task->lesson_id = $new_lesson_id;
    				$new_task->team_member_id = $global_task->default_team_member_id;
    				$new_task->create();
    			}
    		} 
		  }
  		
  		$_SESSION["message"] = "{$database->escape_value($_POST['series_name'])} has been created.";
      redirect_to("language-series.php?id={$new_language_series_id}");
		}  
  }
  
  $levels = Level::find_all();
	$languages = Language::find_all();
?>

<?php include_layout_template('header.php'); ?>
	<div class="small-12 medium-8 medium-centered columns">
	
	  <div id="breadcrumbs" class="row">
    	<ul class="breadcrumbs">
    		<li><a href="lesson-db.php">Lesson DB</a></li>
    		<li><a href="series.php?id=<?php echo $series->id; ?>"><?php echo $series->title; ?></a></li>
    		<li class="current">
    			<a href="#">
    				Add New Language in <?php echo $series->title; ?>
    			</a>
    		</li>
    	</ul>
    </div>
	
  	<?php if($message) { ?>
    <div data-alert class="alert-box">
      <?php echo $message; ?>
      <a href="#" class="close">&times;</a>
    </div>
    <?php } ?>
    	
    <h4>Create new language in series: <?php echo $series->title; ?></h4>
		
		<form action="new-language-series.php?inSeries=<?php echo $series->id; ?>" method="POST" id="new_series_form">
			<label><span data-tooltip class="has-tip" title="This should be the marketing-friendly title.">Name</span> 
			<input type="text" size="50" name="new_language_series_name" id="new_language_series_name" value="<?php echo $_SESSION["new_language_series_name"]; ?>"></label>
			<div class="row collapse">
        <div class="small-12 medium-4 columns">
          <label>
          <span data-tooltip class="has-tip" title="The language taught in this series">Language</span> 
          <select name="new_language_series_language_id" id="new_language_series_language_id">
    				<?php foreach($languages as $language) {
    					echo "<option value='{$language->id}'";
    					if ($language->name == $_SESSION["new_language_series_language_id"]) {
    						echo " selected";
    					}
    					echo ">{$language->name}</option>";
    				} ?>
          </select></label>
        </div>
        <div class="small-12 medium-4 columns">
    			
    			<label><span data-tooltip class="has-tip" title="The difficulty level of this series.">Level</span> 
    			<select name="new_language_series_level_id" id="new_language_series_level_id">
    				<?php foreach($levels as $level) {
    					echo "<option value='{$level->id}'";
    					if($_SESSION["new_language_series_level_id"] == $level->id) {
      					echo " selected";
    					}
    					echo ">{$level->name}</option>";
    				} ?>
    			</select></label>
        </div>
        <div class="small-12 medium-4 columns">
  			
    			<label><span data-tooltip class="has-tip" title="Will automatically create this many lessons and populate them with the correct tasks. You can still add and remove lessons later.">Total Lessons</span> 
    			<select name="new_language_series_total_lessons" id="new_language_series_total_lessons">
    			<?php for($i=0; $i<=50; $i++) {
    					echo "<option value='{$i}'";
    					if ($_SESSION["new_language_series_total_lessons"] == $i) {
      					echo " selected";
    					}
    					echo ">{$i}</option>";
    					} ?>
    			</select></label>
        </div>
			</div>
			<div>
			<input type="submit" name="new_language_series" class="action button" id="new_language_series">
		</form>
		  </div>
	</div>
	<script src="script.js"></script>
<?php include_layout_template('footer.php'); ?>