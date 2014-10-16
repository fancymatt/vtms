<?php require_once("../includes/initialize.php"); ?>
<?php	
	confirm_logged_in();
	$lesson_id = $db->escape_value($_GET['id']);
	$lesson = Lesson::find_by_id($lesson_id);
	if (!$lesson->title) {
		redirect_to("qa.php");
	}
	$language_series = LanguageSeries::find_by_id($lesson->language_series_id);
	$language = Language::find_by_id($language_series->language_id);
	$series = Series::find_by_id($language_series->series_id);
	$logged_in_user = User::find_by_id($session->user_id);
	
	if($_POST['edited_lesson']) {
		$lesson = Lesson::find_by_id($lesson_id);
		$lesson->title = $_POST['edited_lesson_title'];
		$lesson->trt = ($_POST['edited_lesson_trt_minutes'] * 60) + $_POST['edited_lesson_trt_seconds'];
		$lesson->publish_date = $_POST['edited_lesson_publish_date'];
		$lesson->qa_log = $_POST['edited_qa_log'];
		$lesson->qa_url = $_POST['edited_qa_url'];
		$lesson->update();
		$_SESSION['message'] = "Lesson details updated";
		redirect_to("lesson.php?id={$lesson_id}");
	}
	
	if($_POST['changed_qa_log']) {
		$qa_lesson_id = $_POST['qa_lesson_id'];
		$qa_log = $_POST['qa_log'];
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->qa_log = $qa_log;
		$lesson->update();
		$_SESSION['message'] = "You've updated the QA Log to: '" . $qa_log . "'";
		redirect_to("lesson.php?id={$lesson_id}");
	}
	
	
	// Checking and Archiving
	
	if($_POST['language_checked_lesson']) {
	  $current_time = new DateTime(null, new DateTimeZone('UTC'));
	  $lesson->checked_language_time = $current_time->format('Y-m-d H:i:s');
	  $lesson->checked_language = 1;
	  $lesson->update();
	  $_SESSION['message'] = "Lesson language checked.";
	  redirect_to("lesson.php?id={$lesson_id}");
	}
	
	if($_POST['language_unchecked_lesson']) {
	  unset($lesson->checked_language_time);
	  $lesson->checked_language = 0;
	  $lesson->update();
	  $_SESSION['message'] = "Lesson language unchecked.";
	  redirect_to("lesson.php?id={$lesson_id}");
	}
	
	if($_POST['video_checked_lesson']) {
	  $current_time = new DateTime(null, new DateTimeZone('UTC'));
	  $lesson->checked_video_time = $current_time->format('Y-m-d H:i:s');
	  $lesson->checked_video = 1;
	  $lesson->update();
	  $_SESSION['message'] = "Lesson video checked.";
	  redirect_to("lesson.php?id={$lesson_id}");
	}
	
	if($_POST['video_unchecked_lesson']) {
	  unset($lesson->checked_video_time);
	  $lesson->checked_video = 0;
	  $lesson->update();
	  $_SESSION['message'] = "Lesson video unchecked.";
	  redirect_to("lesson.php?id={$lesson_id}");
	}
	
	if($_POST['files_moved_lesson']) {
	  $current_time = new DateTime(null, new DateTimeZone('UTC'));
	  $lesson->files_moved_time = $current_time->format('Y-m-d H:i:s');
	  $lesson->files_moved = 1;
	  $lesson->update();
	  $_SESSION['message'] = "Lesson files moved.";
	  redirect_to("lesson.php?id={$lesson_id}");
	}
	
	if($_POST['files_unmoved_lesson']) {
	  unset($lesson->files_moved_time);
	  $lesson->files_moved = 0;
	  $lesson->update();
	  $_SESSION['message'] = "Lesson files unmoved.";
	  redirect_to("lesson.php?id={$lesson_id}");
	}
	
	if($_POST['detect_lesson']) {
	  $current_time = new DateTime(null, new DateTimeZone('UTC'));
	  $lesson->detected_time = $current_time->format('Y-m-d H:i:s');
	  $lesson->is_detected = 1;
	  $lesson->update();
	  $_SESSION['message'] = "Lesson detected.";
	  redirect_to("lesson.php?id={$lesson_id}");
	}
	
	if($_POST['undetect_lesson']) {
	  unset($lesson->time_detected);
	  $lesson->is_detected = 0;
	  $lesson->update();
	  $_SESSION['message'] = "Lesson undetected.";
	  redirect_to("lesson.php?id={$lesson_id}");
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
<?php $page_title = ucwords($language->code)." ".ucwords($series->code)." ".$lesson->number; ?>

<?php include_layout_template('header.php'); ?>
	
	<div class="small-12 medium-8 medium-centered columns">
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
	</div>
	
	<?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	
	<div class="small-12 medium-8 medium-centered columns">
		<h3><?php echo $language_series->language_series_title." (".ucwords($language_series->level_code).")"; ?>
		<?php echo $lesson->number.". ".$lesson->title; ?></h3>
	</div>
	
	<div class="small-12 medium-8 medium-centered columns">
  	<div class="task-sheet-tabs">
  		<ul class="tabs" data-tab>
  			<li class="tab-title active"><a href="#panel-tasks">Tasks</a></li>
  			<li class="tab-title"><a href="#panel-script">Script</a></li>
  			<li class="tab-title"><a href="#panel-issues">Issues</a></li>
  			<li class="tab-title"><a href="#panel-edit">Edit</a></li>
  		</ul>
  	</div>
  	
  	<div class="tabs-content">
  		<div class="content active" id="panel-tasks">
			  <h3 class="group-heading">Assets</h3>
         <ol class="group">
						<?php foreach($assets as $task): ?> <!-- For every task -->
						<div class="group-item<?php if($task->is_completed) { echo " ready"; } ?>">
              <div class="member">
                <div class="member-image">
                  <img src="img/headshot-<?php echo strtolower($task->team_member_name); ?>.png">
                </div>
                <p class="member-name">
          				<?php if($session->is_admin()) {
        				    echo "<a href='task-sheet.php?member={$task->team_member_id}'>{$task->team_member_name}</a>";  
        				  } else {
          				  echo $task->team_member_name;
        				  } ?>
                </p>
      				</div>
      				<div class="task-info">
        				<p class="task-title"><?php echo $task->task_name; ?></p>
        				<p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
      				</div>
      				<?php if($session->is_admin()) { ?>
              <div class="actions">
      					<a class="action-item" href="edit-task.php?id=<?php echo $task->id; ?>">Edit</a>
              </div>
              <?php } ?>
      			</div>
						<?php endforeach; ?> <!-- End for every asset -->
         </ol>
				
				<h3 class="group-heading">Tasks</h3>
				<ol class="group">
						<?php foreach($tasks as $task): ?> <!-- For every task -->
						<div class="group-item<?php if($task->is_completed) { echo " ready"; } ?>">
              <div class="member">
                <div class="member-image">
                  <img src="img/headshot-<?php echo strtolower($task->team_member_name); ?>.png">
                </div>
                <p class="member-name">
          				<?php if($session->is_admin()) {
        				    echo "<a href='task-sheet.php?member={$task->team_member_id}'>{$task->team_member_name}</a>";  
        				  } else {
          				  echo $task->team_member_name;
        				  } ?>
                </p>
      				</div>
      				<div class="task-info">
        				<p class="task-title"><?php echo $task->task_name; ?></p>
        				<p class="date">
        				  <?php 
        				  if($task->is_completed) {
        				    echo "Completed";
        				    if($task->completed_time > 0) {
          				    echo " on ".$logged_in_user->local_time($task->completed_time);
        				    } 
        				    echo " in ".seconds_to_timecode($task->time_actual, 6);
        				  } else {
          				  echo "Due ".$task->task_due_date;
        				  }
        				  ?>
        				 </p>
      				</div>
              <?php if($session->is_admin()) { ?>
              <div class="actions">
      					<a class="action-item" href="edit-task.php?id=<?php echo $task->id; ?>">Edit</a>
              </div>
              <?php } ?>
      			</div>
						<?php endforeach; ?> <!-- End for every asset -->
					</ol>
			</div>
  		<div class="content" id="panel-script">
  		  <div class="panel centered">
          <a href="lesson-script.php?id=<?php echo $lesson->id; ?>" class="action button">Full Script Page</a>
        </div>
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
  		<div class="content" id="panel-issues">
  			<div class="panel centered">
  			  <label>Current QA Log
  					<form action='lesson.php?id=<?php echo $lesson->id; ?>' method='post'>
  					<input type='text' size=70 name='qa_log' value='<?php echo $lesson->qa_log; ?>'>
  					<input type='hidden' name='qa_lesson_id' value='<?php echo $lesson->id; ?>'>
  					<input type='submit' class="action button" name='changed_qa_log'>
  					</form>
  				</label>
  			</div>
        <h3 class="group-heading">Issues</h3>
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
						<?php if($all_issues) { ?>
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
						<?php } else { ?>
							<tr>
								<td colspan="5">No issues for this lesson</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
  			<h3 class="group-heading">Add an Issue</h3>
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
  		
  		<div class="content" id="panel-edit">
    		<div class="small-12 columns panel">
				  <form action='lesson.php?id=<?php echo $lesson->id; ?>' method='post'>
          <label>QA Log<input type='text' size=60 name='edited_qa_log' value='<?php echo $lesson->qa_log; ?>'></label>
          <label>QA URL<input type='text' size=60 name='edited_qa_url' value='<?php echo $lesson->qa_url; ?>'></label>
          <label>Title <input type="text" size="50" name="edited_lesson_title" value="<?php echo $lesson->title; ?>"></label>
          <label>Publish Date<input type="text" size="50" name="edited_lesson_publish_date" value="<?php echo $lesson->publish_date; ?>"></label>
  				<div class="small-6 columns">
  					<label>TRT Minutes</label>
  					<select name="edited_lesson_trt_minutes" id="edited_lesson_trt_minutes">
  						<?php for($i=0; $i<20; $i++) {
  							echo "<option value='{$i}'";
  							if ($i == $trt_minutes) {
  								echo " selected";
  							}
  							echo ">{$i}</option>";
  						} ?>
  				  </select>
				  </div>
				  <div class="small-6 columns">
					  <label>TRT Seconds</label>
            <select name="edited_lesson_trt_seconds" id="edited_lesson_trt_seconds">
						<?php for($i=0; $i<60; $i++) {
							echo "<option value='{$i}'";
							if ($i == $trt_seconds) {
								echo " selected";
							}
							echo ">{$i}</option>";
						} ?>
					  </select></p>
  				</div>
        
          <div class="small-12 columns">
            <input type="hidden" name="edited_lesson_id" value="<?php echo $current_record->id; ?>">
            <p><input type="submit" class="action button" name="edited_lesson" id="edited_lesson" value="Edit"></p> 
            </form>
          </div>
        </div>
        
        <div class="large-3 medium-6 small-12 columns text-center panel">
          <p>
            <?php if(!$lesson->checked_language) { ?>
				    <form action='lesson.php?id=<?php echo $lesson->id; ?>' method='post'>
            <input type="submit" class="action button" value="Language Checked" name="language_checked_lesson">
				    </form>
            <?php } else { ?>
            <p>Language Checked</p>
				      <?php if($lesson->checked_language_time > 0) {
              echo "<p>";
              echo date("M jS g:i a", strtotime($logged_in_user->local_time($lesson->checked_language_time)));
              echo "</p>";
				      } ?>
            <p><form action='lesson.php?id=<?php echo $lesson->id; ?>' method='post'>
            <input type="submit" class="action button" value="Uncheck Language" name="language_unchecked_lesson">
				    </form>
            <?php } ?>
				  </p>
        </div>
        
        <div class="large-3 medium-6 small-12 columns text-center panel">
          <p>
            <?php if(!$lesson->checked_video) { ?>
				    <form action='lesson.php?id=<?php echo $lesson->id; ?>' method='post'>
            <input type="submit" class="action button" value="Video Checked" name="video_checked_lesson">
				    </form>
            <?php } else { ?>
            <p>Video Checked</p>
				      <?php if($lesson->checked_video_time > 0) {
              echo "<p>";
              echo date("M jS g:i a", strtotime($logged_in_user->local_time($lesson->checked_video_time)));
              echo "</p>";
				      } ?>
            <p><form action='lesson.php?id=<?php echo $lesson->id; ?>' method='post'>
            <input type="submit" class="action button" value="Uncheck Video" name="video_unchecked_lesson">
				    </form>
            <?php } ?>
				  </p>
        </div>
        
        <div class="large-3 medium-6 small-12 columns text-center panel">
          <p>
            <?php if(!$lesson->files_moved) { ?>
				    <form action='lesson.php?id=<?php echo $lesson->id; ?>' method='post'>
            <input type="submit" class="action button" value="Move Files" name="files_moved_lesson">
				    </form>
            <?php } else { ?>
            <p>Files Moved</p>
				      <?php if($lesson->files_moved_time > 0) {
              echo "<p>";
              echo date("M jS g:i a", strtotime($logged_in_user->local_time($lesson->files_moved_time)));
              echo "</p>";
				      } ?>
            <p><form action='lesson.php?id=<?php echo $lesson->id; ?>' method='post'>
            <input type="submit" class="action button" value="Unmove Files" name="files_unmoved_lesson">
				    </form>
            <?php } ?>
				  </p>
        </div>
        
        <div class="large-3 medium-6 small-12 columns text-center panel">
          <p>
            <?php if(!$lesson->is_detected) { ?>
				    <form action='lesson.php?id=<?php echo $lesson->id; ?>' method='post'>
            <input type="submit" class="action button" value="Detect Lesson" name="detect_lesson">
				    </form>
            <?php } else { ?>
            <p>Detected</p>
				      <?php if($lesson->detected_time > 0) {
              echo "<p>";
              echo date("M jS g:i a", strtotime($logged_in_user->local_time($lesson->detected_time)));
              echo "</p>";
				      } ?>
            <p><form action='lesson.php?id=<?php echo $lesson->id; ?>' method='post'>
            <input type="submit" class="action button" value="Undetect Lesson" name="undetect_lesson">
				    </form>
            <?php } ?>
				  </p>
        </div>

          
			</div>
		</div>
  </div>
</div>

<?php include_layout_template('footer.php'); ?>