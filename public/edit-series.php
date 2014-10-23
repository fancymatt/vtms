<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	
	if($_POST['edited_series']) {
		$edited_series_title = $database->escape_value($_POST['edited_series_title']);
		$edited_series_code = $database->escape_value($_POST['edited_series_code']);
		$edited_series_shot_at = $database->escape_value($_POST['shot_at']);
		$edited_series_checkable_at = $database->escape_value($_POST['checkable_at']);
		$edited_series_id = $database->escape_value($_POST['edited_series_id']);
		
		$required_fields = array("edited_series_title", "edited_series_code", "shot_at", "checkable_at");
		validate_presences($required_fields);
		
		$edited_series = Series::find_by_id($edited_series_id);
		
		$edited_series->title = $edited_series_title;
		$edited_series->code = $edited_series_code;
		$edited_series->shot_at = $edited_series_shot_at;
		$edited_series->checkable_at = $edited_series_checkable_at;
		$edited_series->update();
		redirect_to("series.php?id={$edited_series_id}");	
		
		if(!empty($session->errors)) {
			$_SESSION["errors"] = $errors;
		}
	}
	
	if($_POST['deleted_series']) {
		$deleted_series_id = $database->escape_value($_POST['deleted_series_id']);
		$deleted_series = Series::find_by_id($deleted_series_id);
		//$child_language_series = LanguageSeries::find_all_child_for_parent()
		$deleted_series->delete();
		redirect_to("lesson-db.php");
	}
	
	$current_series_id = $_GET['id'];
	$current_series = Series::find_by_id($current_series_id);
	//if (!$current_series->title) {
	//	redirect_to("lesson-db.php");
	//}	
	
	$global_tasks = GlobalTask::get_all_global_tasks_for_series($current_series_id);
	$global_assets = GlobalTask::find_all_asset_tasks_for_series($current_series_id);
?>

<?php include_layout_template('header.php'); ?>

<div class="small-12 medium-8 medium-centered columns">
	<?php if($message) { ?>
  <div data-alert class="alert-box">
    <?php echo $message; ?>
    <a href="#" class="close">&times;</a>
  </div>
  <?php } ?>

	<div class="panel">
		<h4>Edit Series: <?php echo $current_series->title; ?></h4>
		<form action="edit-series.php?id=<?php echo $current_series_id; ?>" method="POST">
			<p><label for="series_name">Name:</label> <input type="text" size="50" name="edited_series_title" value="<?php echo $current_series->title; ?>"></p>
			<p><label for="series_code">Code:</label> <input type="text" name="edited_series_code" value="<?php echo $current_series->code; ?>"></p>
			<p><label for="shot_at">Shot at:</label> <input type="number" name="shot_at" value="<?php echo $current_series->shot_at; ?>"></p>
			<p><label for="checkable_at">Checkable at:</label> <input type="number" name="checkable_at" value="<?php echo $current_series->checkable_at; ?>"></p>
			<input type="hidden" name="edited_series_id" value="<?php echo $current_series->id; ?>">
			<p><input type="submit" name="edited_series" id="edited_series" class="action button"></p>
		</form>
	</div>
	
	<div class="panel">
		<h4>Global Assets</h4>
		<table><tr><th>Task</th><th>Default Member</th><th>Actionable At</th><th>Completion Value</th><th>Due Date Offset</th><th>Actions</th></tr>
		<?php
			if (!$global_assets) {
				echo "<tr><td>No global tasks set</td><td></td><td></td></tr>";
			} else {
				foreach($global_assets as $global_task) {
				if($global_task->default_team_member_id) {
					$default_member = Member::find_by_id($global_task->default_team_member_id);
					$default_member_name = $default_member->first_name;
				} else {
					$default_member_name = "Not set";
				}
				echo "<tr>";
				echo "<td>" . $global_task->task_name . "</td>";
				echo "<td>" . $default_member_name . "</td>";
				echo "<td>" . $global_task->actionable_at . "</td>";
				echo "<td>" . $global_task->completion_value . "</td>";
				echo "<td>" . $global_task->due_date_offset . "</td>";
				echo "<td><a href='global-task-stats.php?id=" . $global_task->id . "'>View</a> | <a href='edit-task-global.php?id=" . $global_task->id . "'>Edit</a></td>";
				echo "</tr>";
				}
			}
		?>
		</table>
	</div>
	
	<div class="panel">
		<h4>Global Tasks</h4>
		<table><tr><th>Task</th><th>Default Member</th><th>Actionable At</th><th>Completion Value</th><th>Due Date Offset</th><th>Actions</th></tr>
		<?php
			if (!$global_tasks) {
				echo "<tr><td>No global tasks set</td><td></td><td></td></tr>";
			} else {
				foreach($global_tasks as $global_task) {
				if($global_task->default_team_member_id) {
					$default_member = Member::find_by_id($global_task->default_team_member_id);
					$default_member_name = $default_member->first_name;
				} else {
					$default_member_name = "Not set";
				}
				echo "<tr>";
				echo "<td>" . $global_task->task_name . "</td>";
				echo "<td>" . $default_member_name . "</td>";
				echo "<td>" . $global_task->actionable_at . "</td>";
				echo "<td>" . $global_task->completion_value . "</td>";
				echo "<td>" . $global_task->due_date_offset . "</td>";
				echo "<td><a href='global-task-stats.php?id=" . $global_task->id . "'>View</a> | <a href='edit-task-global.php?id=" . $global_task->id . "'>Edit</a></td>";
				echo "</tr>";
				}
			}
		?>
		</table>
		<form action="edit-series.php?id=<?php echo $current_series_id; ?>" method="POST">
			<input type="hidden" name="deleted_series_id" id="deleted_series_id" value="<?php echo $current_series->id; ?>">
			<input type="submit" name="deleted_series" id="deleted_series" class="button alert" value="Delete Series" onclick="return confirm('Are you sure you want to delete this item?')">
		</form>
	</div>
</div>

<?php include_layout_template('footer.php'); ?>