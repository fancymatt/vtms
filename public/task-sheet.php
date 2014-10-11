<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$team_member_id = $db->escape_value($_GET['member']);
	$team_member = Member::find_by_id($team_member_id);
	$logged_in_user = User::find_by_id($session->user_id);
	$active_shift = Shift::get_active_shift_for_member($team_member_id);
	$active_activity = Activity::get_active_activity_for_member($team_member_id);
	$current_time = new DateTime(null, new DateTimeZone('UTC'));
	
  if($active_activity->activity == "Fixing issues") {
	  $working_on_issues = TRUE;
	}
	
	if(isset($_SESSION['last_shift'])) {
  	// We just finished a shift and should be back at the start page
  	$last_shift = Shift::find_by_id($_SESSION['last_shift']);
  	$last_shift_activities = Activity::find_all_activities_for_shift($last_shift->id);
  	unset($_SESSION['last_shift']);
  }
	
	if (!$session->is_admin()) {
		// then you'd better be this user
		if ($_SESSION['user_id'] != $team_member->user_id) {
			redirect_to('login.php');
		}
	}

	if(isset($_POST['start_shift'])) {
		$shift = new Shift();
		$shift->clock_in_team_member($team_member_id);
		$_SESSION['message'] = "A new shift has begun.";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['start_fixing_issues']) {
	  // Whatever your current activity is, complete it
	  if(is_object($active_activity)) {
  		$active_activity->is_active = 0;
  		$active_activity->is_completed = 1;
  		$active_activity->time_end = $current_time->format('Y-m-d H:i:s');
  		$active_activity->update();
  		$active_tasks = Task::get_active_tasks_for_member($team_member_id);
			if($active_tasks) {
  			foreach($active_tasks as $task) {
    			$task->deactivate_task();
  			}
			}
	  }
	
	  $activity = new Activity();
		$activity->shift_id = $active_shift->id;
		$activity->time_start = $current_time->format('Y-m-d H:i:s');
		$activity->is_active = 1;
		$activity->activity = "Fixing issues";
		$activity->create();
		
  	$_SESSION['message'] = "You've begun fixing issues.";
  	redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['stop_fixing_issues']) {
  	if(is_object($active_activity)) {
  		$active_activity->is_active = 0;
  		$active_activity->is_completed = 1;
  		$active_activity->time_end = $current_time->format('Y-m-d H:i:s');
  		$active_activity->update();
	  }
	  $working_on_issues = FALSE;
	  
	  $_SESSION['message'] = "You've stopped fixing issues.";
  	redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['submitted_custom_status']) {
	  $custom_status = $db->escape_value($_POST['custom_status']);
	  
	  $activity = new Activity();
		$activity->shift_id = $active_shift->id;
		$activity->time_start = $current_time->format('Y-m-d H:i:s');
		$activity->is_active = 1;
		$activity->activity = $custom_status;
		$activity->create();
		
  	$_SESSION['message'] = "You've begun ".stripslashes($custom_status);
  	redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['stop_custom_task']) {
  	// Whatever your current activity is, complete it
	  if(is_object($active_activity)) {
  		$active_activity->is_active = 0;
  		$active_activity->is_completed = 1;
  		$active_activity->time_end = $current_time->format('Y-m-d H:i:s');
  		$active_activity->update();
  		$_SESSION['message'] = "You've stopped ".$active_activity->activity;
      redirect_to("task-sheet.php?member={$team_member_id}");
	  }
	}
	
	if($_POST['end_shift']) {
	  // If there is a task going on, deactivate it
	  $active_tasks = Task::get_active_tasks_for_member($team_member_id);
	  foreach($active_tasks as $active_tasks) {
      $active_tasks->deactivate_task();
	  }
	  
	  // Whatever your current activity is, complete it
	  $activity = Activity::get_active_activity_for_member($team_member_id);
	  
	  if(is_object($activity)) {
  		$activity->is_active = 0;
  		$activity->is_completed = 1;
  		$activity->time_end = $current_time->format('Y-m-d H:i:s');
  		$activity->update();
	  }
	  	  
		$active_shift->clock_out_team_member($team_member_id);
		$_SESSION['message'] = "You have been clocked out. おつかれさま！";
		$_SESSION['last_shift'] = $_POST['shift_id'];
		redirect_to("task-sheet.php?member={$team_member_id}");
	}

	if($_POST['task_activated']) {
	  // Deactivate whatever activity you're working on
	  if(is_object($active_activity)) {
			$active_activity->is_active = 0;
			$active_activity->is_completed = 1;
			$active_activity->time_end = $current_time->format('Y-m-d H:i:s');
			$active_activity->update();
			$active_tasks = Task::get_active_tasks_for_member($team_member_id);
			if($active_tasks) {
  			foreach($active_tasks as $task) {
    			$task->deactivate_task();
  			}
			}
		}
	
		$activated_task_id = $_POST['task_id'];
		$activated_task = Task::find_by_id($activated_task_id);
		$activated_task->activate_task();
		$activated_global_task = GlobalTask::find_by_id($activated_task->global_task_id);

		$activity = new Activity();
		$activity->shift_id = $active_shift->id;
		$activity->time_start = $current_time->format('Y-m-d H:i:s');
		$activity->task_id = $activated_task_id;
		$activity->is_active = 1;
		$activity->activity = "Working on a task";
		$activity->create();
		
		$_SESSION['message'] = $activated_task->language_name . " ". $activated_task->series_name." #".$activated_task->lesson_number . " - ". $activated_task->task_name . " has been activated.";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['task_deactivated']) {
		$deactivated_task_id = $_POST['task_id'];
		$deactivated_task = Task::find_by_id($deactivated_task_id);
		
		if(is_object($active_activity)) {
			$active_activity->is_active = 0;
			$active_activity->is_completed = 1;
			$active_activity->time_end = $current_time->format('Y-m-d H:i:s');
			$active_activity->activity = "Worked on task";
			$active_activity->update();
		}
		$deactivated_task->deactivate_task();
		
    $_SESSION['message'] = "You've paused the task you were working on.";
  	redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['task_completed']) {
		$completed_task_id = $_POST['task_id'];
		$completed_task = Task::find_by_id($completed_task_id);
		
		$activity = Activity::get_active_activity_for_member($team_member_id);
		$activity->is_active = 0;
		$activity->is_completed = 1;
		$activity->time_end = $current_time->format('Y-m-d H:i:s');
		$activity->activity = "Completed task";
		$activity->update();
		
		$completed_task->complete_task();
		
		$_SESSION['message'] = "You've completed a task";
		$_SESSION['completed_task_id'] = $completed_task_id;
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['asset_delivered']) {
		$completed_task_id = $_POST['task_id'];
		$completed_task = Task::find_by_id($completed_task_id);
		$completed_task->deliver_asset();
		
		$_SESSION['message'] = "Asset Delivered";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['asset_completed']) {
		$completed_task_id = $_POST['task_id'];
		$completed_task = Task::find_by_id($completed_task_id);
		$completed_task->complete_asset();
		
		$_SESSION['message'] = "Asset Completed";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['asset_completed_and_delivered']) {
		$completed_task_id = $_POST['task_id'];
		$completed_task = Task::find_by_id($completed_task_id);
		$completed_task->complete_and_deliver_asset();
		
		$_SESSION['message'] = "Asset Completed and Delivered";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['issue_completed']) {
		$completed_issue_id = $_POST['issue_id'];
		$completed_issue = Issue::find_by_id($completed_issue_id);
		$completed_issue->complete_issue($active_activity->id);
		$_SESSION['message'] = "You fixed an issue"; 
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($team_member_id) {
		$tasks_actionable = Task::get_actionable_tasks_for_member($team_member_id);
		$tasks_active = Task::get_active_tasks_for_member($team_member_id);
		$issues_actionable = Issue::get_unfinished_issues_for_member($team_member_id);
		$assets_actionable = Task::get_actionable_assets_for_member($team_member_id);
		$assets_deliverable = Task::get_deliverable_assets_for_member($team_member_id);
		
		if($tasks_active) {
		  foreach($tasks_active as $task) {
    		$task_active_global_task = GlobalTask::find_by_id($task->global_task_id);
      	$task_active_global_task_stats = GlobalTaskStatistic::find_all_child_for_parent($task_active_global_task->id, "task", "TaskGlobal", " AND task.isCompleted = 1 ");
      	$task_active_member_task_stats = GlobalTaskStatistic::find_all_child_for_parent($task_active_global_task->id, "task", "TaskGlobal", " AND task.isCompleted = 1 AND task.fkTeamMember= {$team_member_id} "); 
      	$global_task_stat = $task_active_global_task_stats[0];
      	$member_task_stat = $task_active_member_task_stats[0];  
		  }
		}
		
	} else {
		$message = "Could not find user";
	}

?>

<?php include_layout_template('header.php'); ?>

<?php 
if($message) { ?>
<div data-alert class="alert-box">
  <?php echo $message; ?>
  <a href="#" class="close">&times;</a>
</div>
<?php } ?>

<?php if(is_object($active_shift)) { ?>

<div class="small-12 medium-8 medium-centered columns">
  <h3><?php echo $team_member->first_name."'s Task Sheet"; ?></h3>
</div>

<?php if($_SESSION['completed_task_id']) { 
  $completed_task = Task::find_by_id($_SESSION['completed_task_id']);
  $completed_global_task = GlobalTask::find_by_id($completed_task->global_task_id);
  $global_task_stats = GlobalTaskStatistic::find_all_child_for_parent($completed_global_task->id, "task", "TaskGlobal", " AND task.isCompleted = 1 ");
	$member_task_stats = GlobalTaskStatistic::find_all_child_for_parent($completed_global_task->id, "task", "TaskGlobal", " AND task.isCompleted = 1 AND task.fkTeamMember= {$team_member_id} "); 
	$global_task_stat = $global_task_stats[0];
	$member_task_stat = $member_task_stats[0];
?>
<div class="small-12 medium-8 medium-centered columns">
  <div class="panel">
    <p>Last Task: <span class="strong"><?php echo $completed_task->display_full_task_lesson()." - ".$completed_task->task_name; ?></span></p>
    <p>Time: <span class="strong"><?php echo seconds_to_timecode($completed_task->actual_time, 6); ?></span></p>
    <p>Your Average Time: <span class="strong"><?php echo seconds_to_timecode($member_task_stat->average_time, 6); ?></span></p>
    <p>Team Average Time: <span class="strong"><?php echo seconds_to_timecode($global_task_stat->average_time, 6); ?></span></p>
    <p>Your Times Completed: <span class="strong"><?php echo $member_task_stat->times_completed; ?></span></p>
    <p>Team Times Completed: <span class="strong"><?php echo $global_task_stat->times_completed; ?></span></p>
  </div>
</div>
<?php
  unset($_SESSION['completed_task_id']);
} ?>

<div class="small-12 medium-8 medium-centered columns">
	<h3 class="group-heading">Your Shift</h3>
  <ol class="group">
  <?php
  $activities = Activity::find_all_activities_for_shift($active_shift->id); ?>
    <div class="group-item">
      <div class="member">
        <div class="member-image">
          <img src="img/headshot-<?php echo strtolower($team_member->first_name); ?>.png">
        </div>
        <p class="member-name">
  				<?php echo $team_member->first_name; ?>
        </p>
			</div>
			<div class="issue-info">
				<p class="lesson-title"><?php 
				echo date("M jS", strtotime($logged_in_user->local_time($active_shift->clock_in)))." ";
				echo date("g:i a", strtotime($logged_in_user->local_time($active_shift->clock_in)))." - ";
				if($shift->is_active) {
  				echo "now";
				} else {
  				echo date("g:i a", strtotime($logged_in_user->local_time($active_shift->clock_out)));
				} ?>
				</p>
			</div>
			<div class="activity-list">
		<?php if($activities) { ?>
  	  <?php foreach($activities as $activity) : ?>
  		  <div class="activity<?php if($activity->is_active) { echo " active"; } ?>">
  		    <p class="start-time"><?php echo date("g:i a", strtotime($logged_in_user->local_time($activity->time_start)));?></p>
  		    <p class="end-time">
  		    <?php if(!$activity->is_active) { ?>
    		    <?php echo date("g:i a", strtotime($logged_in_user->local_time($activity->time_end))); ?>
  		    <?php } else { echo "now"; } ?>
  		    </p>
    		  <a class="activity-name" href="#">
    		  <?php 
    		  if($activity->task_id) {
    		    $task = Task::find_by_id($activity->task_id);
      		  echo $activity->activity.": ";
      		  echo $task->display_full_task_lesson();
      		  echo " ".$task->task_name;
    		  } else if ($activity->activity == "Fixing issues") {
      		  echo "Fixing ".$activity->issues_fixed." Issues";
    		  } else {
    		    echo ucfirst(stripslashes($activity->activity));
    		  }
    		  ?>
    		  </a>      		  
    		</div>
      <?php endforeach; ?>	
		<?php } else { ?>
  		  <div class="activity">
  		    <a class="activity-name">No activities for this shift</a>
  		  </div>
		<?php } ?>
  		  <div class="group-item-actions">
    		  <form method="post" action="task-sheet.php?member=<?php echo $team_member_id; ?>">
            <input type="hidden" name="shift_id" value="<?php echo $active_shift->id; ?>">
        		<input type="submit" class="action button" name="end_shift" value="End Shift" onclick="return confirm('Are you all done for the day?')">
          </form>
  		  </div>
  		</div>
    </div>
    <div class="group-item">
    <div class="member">
      <div class="member-image">
        <img src="img/headshot-<?php echo strtolower($team_member->first_name); ?>.png">
      </div>
      <p class="member-name">
    		<?php echo $team_member->first_name; ?>
      </p>
    </div>
    
<?php if(is_object($active_activity)) { ?>

  <?php if($tasks_active) {
    foreach($tasks_active as $task) : ?>

    <div class="group-item-body">
      <div class="group-item-header">
        <h3 class="group-item-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(). "</a> ".$task->task_name; ?></h3>
      </div>
      <div class="group-item-content">
        <div class="group-item-metadata">
          <p>Began <?php echo date("g:i a", strtotime($logged_in_user->local_time($task->time_activated))); ?></p>
          <p>Team Average: <?php echo seconds_to_timecode($global_task_stat->average_time, 6); ?></p>
          <p>Your Average: <?php echo seconds_to_timecode($member_task_stat->average_time, 6); ?></p>
        </div>
        <div class="group-item-text">
        </div>
        <div class="group-item-actions">
          <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
  					<input type='hidden' name='task_id' value='<?php echo $task->id; ?>'>
  					<input type='submit' class="action button" name='task_deactivated' value='Break'>
					</form>
          <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
  					<input type='hidden' name='task_id' value='<?php echo $task->id; ?>'>
  					<input type='submit' class="action button" name='task_completed' value='Complete'>
					</form>
        </div>
      </div>
    </div>
  
    <?php endforeach; ?>
    
  <?php } else { // not if(is_object($active_tasks)) -- we're saying it's custom ?>
  
    <div class="group-item-body">
    <div class="group-item-header">
      <h3 class="group-item-title"><?php echo $team_member->first_name. " is ". lcfirst(stripslashes($active_activity->activity)); ?></h3>
    </div>
    <div class="group-item-content">
      <div class="group-item-metadata">
        <p><?php echo "Began ".date("g:i a", strtotime($logged_in_user->local_time($active_activity->time_start))); ?></p>
      </div>
      <div class="group-item-text">
      </div>
      <div class="group-item-actions">
        <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
					<input type='submit' class="action button" name='stop_custom_task' value="I'm Done">
				</form>
      </div>
    </div>
  </div>
    
  <?php } // end if(is_object($tasks_active)) ?>
    
<?php } else { // not if(is_object($active_activity)) ?>
    <div class="group-item-body">
      <div class="row">
        <div class="small-2 columns">
          <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
          <p class="status-prompt"><?php echo $team_member->first_name ?> is</p>
        </div>
        <div class="small-7 columns">
          <input type="text" class="status-entry" name="custom_status">
        </div>
        <div class="small-2 columns">
          <input type="submit" class="button postfix" name="submitted_custom_status">
          </form>
        </div>
      </div>
    </div>
  
  <?php } //end if(is_object($active_activity)) ?>
  </div>
</div>
<br />
<div class="small-12 medium-8 medium-centered columns">
<div class="task-sheet-tabs">
	<ul class="tabs" data-tab>
		<li class="tab-title active"><a href="#panel-tasks">Tasks</a></li>
		<li class="tab-title"><a href="#panel-assets">Assets</a></li>
		<li class="tab-title"><a href="#panel-issues">Issues</a></li>
	</ul>
</div>
	
  <div class="tabs-content">

    <!-- Actionable Tasks -->
      <div class="content active" id="panel-tasks">
        <div class="group-header">
          <h3 class="group-title">Actionable Tasks</h3>
          <div class="group-item-sort-options">
          </div>
        </div>
      <?php if($tasks_actionable) { ?>
        <?php
        foreach($tasks_actionable as $task) : 
        ?>
        <div class="group-item<?php if(strtotime($task->task_due_date) < time()) { echo " overdue"; } ?>">
          <div class="group-item-body">
            <div class="group-item-header">
              <h3 class="group-item-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(). "</a> ".$task->task_name; ?></h3>
            </div>
            <div class="group-item-content">
              <div class="group-item-metadata">
                <p>Due <?php echo date("M jS", strtotime($logged_in_user->local_time($task->task_due_date))); ?></p>
              </div>
              <div class="group-item-text">
              </div>
              <div class="group-item-actions">
                <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
        					<input type='hidden' name='task_id' value='<?php echo $task->id; ?>'>
        					<input type='submit' class="action button" name='task_activated' value='Activate'>
      					</form>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php } ?>
      </div>
      
      <div class="content" id="panel-assets">
        <div class="group-header">
          <h3 class="group-title">Deliverable Assets</h3>
          <div class="group-item-sort-options">
          </div>
        </div>
      <?php if($assets_deliverable) { ?>
        <?php
        foreach($assets_deliverable as $task) : 
        ?>
        <div class="group-item<?php if(strtotime($task->task_due_date) < time()) { echo " overdue"; } ?>">
          <div class="group-item-body">
            <div class="group-item-header">
              <h3 class="group-item-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(). "</a> ".$task->task_name; ?></h3>
            </div>
            <div class="group-item-content">
              <div class="group-item-metadata">
                <p>Due <?php echo date("M jS", strtotime($logged_in_user->local_time($task->task_due_date))); ?></p>
              </div>
              <div class="group-item-text">
              </div>
              <div class="group-item-actions">
                <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
        					<input type='hidden' name='task_id' value='<?php echo $task->id; ?>'>
        					<input type='submit' class="action button" name='asset_delivered' value='Deliver'>
        				</form>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php } ?>
        <div class="group-header">
          <h3 class="group-title">Actionable Assets</h3>
          <div class="group-item-sort-options">
          </div>
        </div>
      <?php if($assets_actionable) { ?>
        <?php
        foreach($assets_actionable as $task) : 
        ?>
        <div class="group-item<?php if(strtotime($task->task_due_date) < time()) { echo " overdue"; } ?>">
          <div class="group-item-body">
            <div class="group-item-header">
              <h3 class="group-item-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(). "</a> ".$task->task_name; ?></h3>
            </div>
            <div class="group-item-content">
              <div class="group-item-metadata">
                <p>Due <?php echo date("M jS", strtotime($logged_in_user->local_time($task->task_due_date))); ?></p>
              </div>
              <div class="group-item-text">
              </div>
              <div class="group-item-actions">
                <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
        					<input type='hidden' name='task_id' value='<?php echo $task->id; ?>'>
        					<input type='submit' class="action button" name='asset_completed' value='Complete'>
        					<input type='submit' class="action button" name='asset_completed_and_delivered' value='Deliver'>
      					</form>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php } ?>
      </div>
  		<div class="content" id="panel-issues">
    		<div class="panel centered">
        <?php if(!$working_on_issues) { ?>
          <form method="post" action="task-sheet.php?member=<?php echo $team_member_id; ?>">
        		<input type="submit" class="button" name="start_fixing_issues" value="Start Fixing Issues">
        	</form>
        <?php } else { ?>
          <form method="post" action="task-sheet.php?member=<?php echo $team_member_id; ?>">
        		<input type="submit" class="button" name="stop_fixing_issues" value="Stop Fixing Issues">
        	</form>
        <?php } ?>
        </div>
  		  <div class="group-header">
          <h3 class="group-title">Actionable Issues</h3>
          <div class="group-item-sort-options">
          </div>
        </div>
      <?php if($issues_actionable) { ?>
        <?php
        foreach($issues_actionable as $issue) : 
        $task = Task::find_by_id($issue->task_id); ?>
        <div class="group-item">
          <div class="group-item-body">
            <div class="group-item-header">
              <h3 class="group-item-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(); ?></a> <?php echo $task->task_name; ?></h3>
            </div>
            <div class="group-item-content">
              <div class="group-item-metadata">
                <p><?php echo "Reported by ".$issue->issue_creator; ?></p>
                <p><?php if($issue->issue_timecode) { echo $issue->issue_timecode; } else { echo "No timecode."; } ?></p>
              </div>
              <div class="group-item-text">
                <p><?php if($issue->issue_body) { echo $issue->issue_body; } else { echo "No text has been submitted."; }; ?></p>
              </div>
              <div class="group-item-actions">
              <?php if($working_on_issues) { ?>
                <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
                    <input type='hidden' name='issue_id' value='<?php echo $issue->id; ?>'>
                    <input type='submit' class="action button" name='issue_completed' value='Fixed'>
                  </form>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php } ?>
  		</div>
<?php } else { // else not if($active_shift) ?>
<?php if(isset($last_shift)) { ?>

<div id="shifts" class="small-12 columns">
		<h3 class="group-heading">Your Day</h3>
    <ol class="group">
      <div class="group-item">
        <div class="member">
          <div class="member-image">
            <img src="img/headshot-<?php echo strtolower($team_member->first_name); ?>.png">
          </div>
          <p class="member-name"><?php echo $team_member->first_name; ?></p>
				</div>
				<div class="issue-info">
  				<p class="lesson-title"><?php 
  				echo date("M jS", strtotime($logged_in_user->local_time($last_shift->clock_in)))." ";
  				echo date("g:i a", strtotime($logged_in_user->local_time($last_shift->clock_in)))." - ";
  				if($shift->is_active) {
    				echo "now";
  				} else {
    				echo date("g:i a", strtotime($logged_in_user->local_time($last_shift->clock_out)));
  				} ?>
  				</p>
				</div>
				<div class="activity-list">
  		<?php if($last_shift_activities) { ?>
    	  <?php foreach($last_shift_activities as $activity) : ?>
    		  <div class="activity<?php if($activity->is_active) { echo " active"; } ?>">
    		    <p class="start-time"><?php echo date("g:i a", strtotime($logged_in_user->local_time($activity->time_start)));?></p>
    		    <p class="end-time">
    		    <?php if(!$activity->is_active) { ?>
      		    <?php echo date("g:i a", strtotime($logged_in_user->local_time($activity->time_end))); ?>
    		    <?php } else { echo "now"; } ?>
    		    </p>
      		  <a class="activity-name" href="<?php echo $activity->task_id;?>">
      		  <?php 
      		  if($activity->task_id) {
      		    $task = Task::find_by_id($activity->task_id);
        		  echo $activity->activity.": ";
        		  echo $task->display_full_task_lesson();
        		  echo " ".$task->task_name;
      		  } else if ($activity->activity == "Fixing issues") {
        		  echo "Fixing ".$activity->issues_fixed." Issues";
      		  } else {
      		    echo ucfirst(stripslashes($activity->activity));
      		  }
      		  ?>
      		  </a>      		  
      		</div>
        <?php endforeach; ?>	
  		<?php } else { ?>
    		  <div class="activity">
    		    <a class="activity-name">No activities for this shift</a>
    		  </div>
  		<?php } ?>
  		  </div>
  		</div>
  
<?php } else { ?>
<div class="row centered">
  <div class="small-12 columns">
    <p class="inspirational">Good morning!</p>
  	<form method="post" action="task-sheet.php?member=<?php echo $team_member_id; ?>">
  		<input type="submit" class="button" name="start_shift" value="Start Shift">
  	</form>
  	<span data-tooltip class="has-tip" title="Pressing the button will begin a new shift, which will represent one day's worth of activities.">What's this?</span>
  	<p></p>
  </div>
</div>
<?php } // end if(isset($last_shift)) ?>
<?php } // end if($active_shift) ?>

<?php include_layout_template('footer.php'); ?>