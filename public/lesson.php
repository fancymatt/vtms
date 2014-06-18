<?php require_once("../includes/initialize.php"); ?>
<?php	
	confirm_logged_in();
	$lesson_id = $db->escape_value($_GET['id']);
	$lesson = Lesson::find_by_id($lesson_id);
	if (!$lesson->title) {
		redirect_to("qa.php");
	}
	$language_series = LanguageSeries::find_by_id($lesson->language_series_id);
	$series = Series::find_by_id($language_series->series_id);
	
	if($_POST['edited_lesson']) {
		$lesson = Lesson::find_by_id($lesson_id);
		$lesson->title = $_POST['edited_lesson_title'];
		$lesson->trt = ($_POST['edited_lesson_trt_minutes'] * 60) + $_POST['edited_lesson_trt_seconds'];
		$lesson->checked_language = $_POST['edited_lesson_checked_language'];
		$lesson->checked_video = $_POST['edited_lesson_checked_video'];
		$lesson->files_moved = $_POST['edited_lesson_files_moved'];
		$lesson->is_detected = $_POST['edited_lesson_is_detected'];
		$lesson->publish_date = $_POST['edited_lesson_publish_date'];
		$lesson->qa_log = $_POST['edited_qa_log'];
		$lesson->qa_url = $_POST['edited_qa_url'];
		$lesson->update();
		$message = "Lesson details updated";
	}
	
	if($_POST['changed_qa_log']) {
		$qa_lesson_id = $_POST['qa_lesson_id'];
		$qa_log = $_POST['qa_log'];
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->qa_log = $qa_log;
		$lesson->update();
		$message = "You've updated the QA Log to: '" . $qa_log . "'";
	}
	
	$trt = $lesson->trt;
	$trt_minutes = (int) ($trt/60);
	$trt_seconds = $trt%60;
	
	$tasks = Task::find_all_tasks_for_lesson($lesson_id);
	$assets = Task::find_all_assets_for_lesson($lesson_id);
	$tasks_and_assets = Task::find_all_assets_and_tasks_for_lesson($lesson->id);
	$shots = Shot::find_all_shots_for_lesson($lesson_id);
	$unfinished_issues = Issue::get_unfinished_issues_for_lesson($lesson_id);
	$all_issues = Issue::get_all_issues_for_lesson($lesson->id);

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
			<li class="current">
				<a href="#">
					<?php echo $lesson->number.". ".$lesson->title; ?>
				</a>
			</li>
		</ul>
	</div>
	
	<div id="page-header" class="row">
		<header class="medium-10 medium-margin-1 columns">
			<h3><?php echo $language_series->language_series_title." (".ucwords($language_series->level_code).")"; ?></h3>
		</header>
	</div>
	
	<?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	
	<div id="tabs" class="row">
		<ul class="tabs" data-tab>
			<li class="tab-title active"><a href="#panel-tasks">Tasks</a></li>
			<li class="tab-title"><a href="#panel-script">Script</a></li>
			<li class="tab-title"><a href="#panel-issues">Issues</a></li>
			<li class="tab-title"><a href="#panel-edit">Edit</a></li>
		</ul>
	</div>
	
	<div class="tabs-content">
		<div class="content active" id="panel-tasks">
			<div id="section-header" class="row">
				<header class="medium-10 medium-margin-1 columns">
					<h4>Assets and Tasks</h4>
				</header>
			</div>
			<div id="task-list-table" class="row">
				<div class="medium-11 medium-margin-1 small-12 columns">
					<table>
						<thead>
							<tr>
								<th width="200">Asset Name</th>
								<th>Team Member</th>
								<th>Actionable?</th>
								<th>Completed?</th>
								<th>Delivered?</th>
								<th>Time Spent</th>
								<?php if($session->is_admin()) echo "<th>Actions</th>";?>
							</tr>
						</thead>
						<tbody>
							<?php foreach($assets as $task): ?> <!-- For every task -->
							<tr
							<?php
							if ($task->is_completed ) {
								echo "class='completed'";
							} else if ($task->is_actionable) {
								echo "class='actionable'";
							} else if ($task->is_completed) {
								echo "class='completed'";
							} ?>
							>
								<td><?php echo $task->task_name; ?></td>
								<td><?php echo $task->team_member_name; ?></td>
								<td><?php echo ($task->is_actionable ? 'yes' : 'no'); ?></td>
								<td><?php echo ($task->is_completed ? 'yes' : 'no'); ?></td>
								<td><?php echo ($task->is_delivered ? 'yes' : 'no'); ?></td>
								<td><?php echo seconds_to_timecode($task->time_actual, 6); ?></td>
								<?php
								if ($session->is_admin()) { ?>
									<td>
										<a href="edit-task.php?id=<?php echo $task->id; ?>">Edit</a>
									</td>
								<?php } ?>
							</tr>
							<?php endforeach; ?> <!-- End for every asset -->
						</tbody>
					</table>
					<table>
						<thead>
							<tr>
								<th width="200">Task Name</th>
								<th>Team Member</th>
								<th>Actionable?</th>
								<th>Completed?</th>
								<th>Delivered?</th>
								<th>Time Spent</th>
								<?php if($session->is_admin()) echo "<th>Actions</th>";?>
							</tr>
						</thead>
						<tbody>	
							<?php foreach($tasks as $task): ?> <!-- For every task -->
							<tr
							<?php
							if ($task->is_completed ) {
								echo "class='completed'";
							} else if ($task->is_actionable) {
								echo "class='actionable'";
							} else if ($task->is_completed) {
								echo "class='completed'";
							} ?>
							>
								<td><?php echo $task->task_name; ?></td>
								<td><?php echo $task->team_member_name; ?></td>
								<td><?php echo ($task->is_actionable ? 'yes' : 'no'); ?></td>
								<td><?php echo ($task->is_completed ? 'yes' : 'no'); ?></td>
								<td><?php echo ($task->is_delivered ? 'yes' : 'no'); ?></td>
								<td><?php echo seconds_to_timecode($task->time_actual, 6); ?></td>
								<?php
								if ($session->is_admin()) { ?>
									<td>
										<a href="edit-task.php?id=<?php echo $task->id; ?>">Edit</a>
									</td>
								<?php } ?>
							</tr>
							<?php endforeach; ?> <!-- End for every asset -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="content" id="panel-script">
			<div id="script" class="row">
				<div id="section-header" class="row">
					<header class="medium-10 medium-margin-1 columns">
						<h4>Script Preview</h4>
					</header>
				</div>
				<div id="shot-list-table"  class="row">
					<div class="medium-11 medium-margin-1 small-12 columns">
						<p><a href="lesson-script.php?id=<?php echo $lesson->id; ?>">Go to full script page</a></p>
						<table class="script">
							<thead>
								<th>Section</th>
								<th>Shot</th>
								<th>Script</th>
								<th>Script English</th>
							</thead>
							<tbody>
								<?php if($shots) { ?>
									<?php foreach($shots as $shot): ?>
										<tr>
											<td><?php echo $shot->section; ?></td>
											<td><?php echo $shot->shot." - ".$shot->type; ?></td>
											<td><?php echo nl2br($shot->script); ?></td>
											<td><?php echo nl2br($shot->script_english); ?></td>
										</tr>
									<?php endforeach; ?>
								<?php } else { ?> <!-- End of if($shots) -->
									<tr>
										<td colspan="5">
											<a href="lesson-script.php?id=<?php echo $lesson->id; ?>">No script. Click to edit.</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="content" id="panel-issues">
			<div id="section-header" class="row">
				<header>
					<h4>Issues</h4>
				</header>
			</div>
			<div id="issues-list" class="row">
					<table>
						<thead>
							<tr>
								<th>Task</th>
								<th>Creator</th>
								<th>Timecode</th>
								<th>Issue</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($all_issues as $issue): ?>
							<?php $task_for_issue_id = Task::find_by_id($issue->task_id); ?>
							<tr>
								<td><?php echo $task_for_issue_id->task_name; ?></td>
								<td><?php echo $issue->issue_creator; ?></td>
								<td><?php echo $issue->issue_timecode; ?></td>
								<td><?php echo $issue->issue_body; ?></td>
								<td><?php echo $issue->is_completed ? "Finished" : "Incomplete"; ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<div id="qa-log" class="row">
					<h3>Current QA Log</h3>
					<form action='lesson.php?id=<?php echo $lesson->id; ?>' method='post'>
					<input type='text' size=70 name='qa_log' value='<?php echo $lesson->qa_log; ?>'>
					<input type='hidden' name='qa_lesson_id' value='<?php echo $lesson->id; ?>'>
					<input type='submit' name='changed_qa_log'>
					</form>
				</div>
			<div id="add-an-issue" class="row">
					<h3>Add an Issue</h3>
					<table>
					<thead>
						<tr>
							<th width="400">Problem</th>
							<th width="400">Task Name</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($tasks_and_assets as $task) {
						$global_task = GlobalTask::find_by_id($task->global_task_id);
						if ($global_task->can_add_issues) { ?>
							<tr>
								<td>
									<a href="issues-for-task.php?id=<?php echo $task->id; ?>">
								<?php echo $global_task->issue_reporting_friendly_text; ?></a>
								</td>
								<td>
									<?php echo $global_task->task_name; ?>
								</td>
							</tr>
						<?php } ?>
					<?php } ?>
					</tbody>
				</table>
				</div>
		</div>
		<div class="content" id="panel-edit">
			<div id="section-header" class="row">
				<header class="medium-10 medium-margin-1 columns">
					<h4>Edit</h4>
				</header>
			</div>
			<div id="edit" class="row">
				<div class="medium-11 medium-margin-1 small-12 columns">
					<form action='lesson.php?series=<?php echo $lesson->series_id; ?>&langSeries=<?php echo $lesson->language_series_id;  ?>&lesson=<?php echo $lesson->id; ?>' method='post'>
						<p>Log: <input type='text' size=60 name='edited_qa_log' value='<?php echo $lesson->qa_log; ?>'></p>
						<p>URL: <input type='text' size=60 name='edited_qa_url' value='<?php echo $lesson->qa_url; ?>'></p>
						<p><label for="edited_lesson_title">Title:</label> <input type="text" size="50" name="edited_lesson_title" value="<?php echo $lesson->title; ?>"></p>
						<p><label for="edited_lesson_publish_date">Publish Date:</label> <input type="text" size="50" name="edited_lesson_publish_date" value="<?php echo $lesson->publish_date; ?>"></p>
						<p><label for="edited_lesson_trt_minutes">TRT:</label> 
						<select name="edited_lesson_trt_minutes" id="edited_lesson_trt_minutes">
							<?php for($i=0; $i<20; $i++) {
								echo "<option value='{$i}'";
								if ($i == $trt_minutes) {
									echo " selected";
								}
								echo ">{$i}</option>";
							} ?>
						</select>
						<select name="edited_lesson_trt_seconds" id="edited_lesson_trt_seconds">
							<?php for($i=0; $i<60; $i++) {
								echo "<option value='{$i}'";
								if ($i == $trt_seconds) {
									echo " selected";
								}
								echo ">{$i}</option>";
							} ?>
						</select></p>
						Language Checked<input type="checkbox" name="edited_lesson_checked_language" value="1" <?php echo $lesson->checked_language ? "checked" : ""; ?>><br />
						Video Checked<input type="checkbox" name="edited_lesson_checked_video" value="1" <?php echo $lesson->checked_video ? "checked" : ""; ?>><br />
						Files Moved<input type="checkbox" name="edited_lesson_files_moved" value="1" <?php echo $lesson->files_moved ? "checked" : ""; ?>><br />
						Lesson Detected<input type="checkbox" name="edited_lesson_is_detected" value="1" <?php echo $lesson->is_detected ? "checked" : "" ?>>
						<input type="hidden" name="edited_lesson_id" value="<?php echo $current_record->id; ?>">
						<p><input type="submit" name="edited_lesson" id="edited_lesson" value="Edit"></p>
					</form>
				</div>
			</div>
		</div>
	</div>

<?php include_layout_template('footer.php'); ?>