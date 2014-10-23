<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	$series_id = $db->escape_value($_GET['inSeries']);
	$is_asset = $db->escape_value($_GET['isAsset']);
	$series = Series::find_by_id($series_id);
		
	if($_POST['create_global_task']) {
  	$global_task = new GlobalTask();
		$global_task->task_name = $db->escape_value($_POST['task_name']);
		$global_task->series_id = $db->escape_value($series_id);
		$global_task->actionable_at = $db->escape_value($_POST['task_actionable_at']);
		$global_task->completion_value = $db->escape_value($_POST['task_completion_value']);
		$global_task->default_team_member_id = $db->escape_value($_POST['task_team_member']);
		$global_task->is_asset = $db->escape_value($_POST['task_is_asset']);
		$global_task->can_add_issues = $db->escape_value($_POST['can_add_issues']);
		$global_task->due_date_offset = $db->escape_value($_POST['due_date_offset']);
		$global_task->issue_reporting_friendly_text = $db->escape_value($_POST['issue_reporting_friendly_text']);
		$global_task->create();
		redirect_to("edit-series.php?id={$global_task->series_id}");
	}

	$team_members = Member::find_all_members();
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
  		<li><a href="series.php?id=<?php echo $series->id; ?>"><?php echo $series->title; ?></a></li>
  		<li><a href="edit-series.php?id=<?php echo $series->id; ?>">Edit</a></li>
  		<li class="current">
  			<a href="#">
  				New Global Task/Asset
  			</a>
  		</li> 
  	</ul>
  </div>

  <div class="panel">
    
    <div class="row">
      <div class="small-12 columns">
		    <h4>New Global Task/Asset</h4>
      </div>
    </div>
    
    <div class="row">
      <div class="small-12 columns">
        <form action="new-task-global.php?inSeries=<?php echo $series->id; ?>" method="post">
        <p><label for="task_name">Task Name: </label><input type="text" name="task_name" value="<?php echo $db->escape_value($_POST['task_name']); ?>"></p>
      </div>
    </div>
    
		<div class="row">
			<div class="small-4 columns">
			  <p><label for="task_actionable_at">Actionable At: </label><input type="number" name="task_actionable_at" value="<?php echo $db->escape_value($_POST['task_actionable_at']); ?>"></p>
			</div>
			<div class="small-4 columns">
			  <p><label for="task_completion_value">Completion Value: </label><input type="number" name="task_completion_value" value="<?php echo $db->escape_value($_POST['task_completion_value']); ?>"></p>
			</div>
			<div class="small-4 columns">
        <p><label for="due_date_offset">Due Date Offset: </label><input type="number" name="due_date_offset" value="<?php echo $db->escape_value($_POST['due_date_offset']); ?>"></p>
			</div>
		</div>
		
		<div class="row">
			 <div class="small-6 columns">
  			 <p><label for="can_add_issues">Can Add Issues: </label><input type="checkbox" name="can_add_issues" value="1" <?php echo $db->escape_value($_POST['can_add_issues']) ? "checked" : ""; ?>></p>
			 </div>
			 <div class="small-6 columns">
  			 <p><label for="task_is_asset">Asset</label><input type="checkbox" name="task_is_asset" value="1" <?php echo $is_asset ? "checked" : ""; ?>></p>
			 </div>
		</div>
		
		<div class="row">
  		<div class="small-12 columns">
			<p><label for="issue_reporting_friendly_text">Friendly Text for Issue Reporting: </label><input type="text" name="issue_reporting_friendly_text" value="<?php echo $db->escape_value($_POST['issue_reporting_friendly_text']); ?>"></p>
  		</div>
		</div>
		
		<div class="row">
  		<div class="small-12 columns">
			  <p><label for="team_member">Default Team Member:</label> <select name="task_team_member">
  			<?php
  				foreach ($team_members as $team_member) {
  					echo "<option value='";
  					echo $team_member->id;
  					echo "'";
  					if ($db->escape_value($_POST['task_team_member']) == $team_member->id) {echo " selected"; }
  					echo ">";
  					echo $team_member->first_name;
  					echo "</option>";
  				} ?>
  			</select></p>
  		</div>
		</div>
		
		<div class="row">
			<p><input type="submit" value="Create" class="action button" name="create_global_task"></p>
      </form>
		</div>
		
  </div>
</div>
<?php include_layout_template('footer.php'); ?>
	