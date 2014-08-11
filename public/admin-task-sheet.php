<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$team_member_id = $db->escape_value($_GET['member']);
	$team_member = Member::find_by_id($team_member_id);
	$logged_in_user = User::find_by_id($session->user_id);
	$active_shift = Shift::get_active_shift_for_member($team_member_id);
	$active_activity = Activity::get_active_activity_for_member($team_member_id);
	$current_time = new DateTime(null, new DateTimeZone('UTC'));
	
	/* TO DO
	
	Allowable actions on this page should be:
	
	Deactivating Tasks
	Ending Shifts
	Completing Tasks (in one click)
	Completing Issues
	Completing or Delivering Assets
	
	IF SHIFT IS ACTIVE
	  Activate Tasks
	  
	*/
	
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin access to view this page.";
		redirect_to('login.php');
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
		$message = $deactivated_task->deactivate_task();
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
		
		$message = $completed_task->complete_task() . "<br />";
		$_SESSION['completed_task_id'] = $completed_task_id;
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['asset_delivered']) {
		$completed_task_id = $_POST['task_id'];
		$completed_task = Task::find_by_id($completed_task_id);
		$completed_task->deliver_asset() . "<br />";
		$message = "Asset Delivered";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['asset_completed']) {
		$completed_task_id = $_POST['task_id'];
		$completed_task = Task::find_by_id($completed_task_id);
		$completed_task->complete_asset() . "<br />";
		$message = "Asset Completed";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['asset_completed_and_delivered']) {
		$completed_task_id = $_POST['task_id'];
		$completed_task = Task::find_by_id($completed_task_id);
		$completed_task->complete_and_deliver_asset() . "<br />";
		$message = "Asset Completed and Delivered";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['issue_completed']) {
		$completed_issue_id = $_POST['issue_id'];
		$completed_issue = Issue::find_by_id($completed_issue_id);
		$completed_issue->complete_issue($active_activity->id);
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($team_member_id) {
		$actionable_tasks = Task::get_actionable_tasks_for_member($team_member_id);
		$active_tasks = Task::get_active_tasks_for_member($team_member_id);
		$actionable_issues = Issue::get_unfinished_issues_for_member($team_member_id);
		$actionable_assets = Task::get_actionable_assets_for_member($team_member_id);
		$deliverable_assets = Task::get_deliverable_assets_for_member($team_member_id);
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
	  <div class="row">
  		<div class="small-12 columns">
  		  <h3><?php echo $team_member->first_name."'s Task Sheet"; ?></h3>
  		</div>
		</div>
    <?php if(is_object($active_activity)) { ?>
      <div class="row"> 
      <div class="small-12 columns">
      <p class="status"><?php echo $team_member->first_name. " is ". lcfirst(stripslashes($active_activity->activity)); ?></p>
      </p>
      <?php if($active_tasks) { ?>         
      <ol class="group">
        <?php
        foreach($active_tasks as $task) : ?>
          <div class="group-item">
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
      				<p class="lesson-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(); ?></a> <?php echo $task->task_name; ?></p>
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
    				<div class="actions">
    				  <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
      					<input type='hidden' name='task_id' value='<?php echo $task->id; ?>'>
      					<input type='submit' name='task_deactivated' value='Deactivate'>
    					</form>
    				  <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
      					<input type='hidden' name='task_id' value='<?php echo $task->id; ?>'>
      					<input type='submit' name='task_completed' value='Complete'>
    					</form>
      			</div>
      		</div>
        <?php endforeach; ?>
      </ol>
      <?php } else { // not if(is_object($active_tasks)) -- we're saying it's custom ?>
      <div>
        <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
					<input type='submit' name='stop_custom_task' value="I'm Done">
				</form>
      </div>
      <?php } // end if(is_object($active_tasks)) ?>
      </div>
    </div>
    <?php } else { // not if(is_object($active_activity)) ?>
    <div class="small-12 columns">
	    <div class="member-status">
	      <p><span data-tooltip class="has-tip" title="If you're working on something that isn't a task, asset, or issue, enter it here.">What are you doing?</span></p>
        <div class="row collapse">
          <div class="small-10 columns">
          <span class="status-name"><?php echo $team_member->first_name; ?> is: </span><form method="post" action="task-sheet.php?member=<?php echo $team_member_id; ?>">
          <input type="text" class="status-entry" name="custom_status">
          </div>
          <div class="small-2 columns">
            <input type="submit" class="button postfix" name="submitted_custom_status">
            </form>
          </div>
        </div>
	    </div>
	  </div>
    <?php } //end if(is_object($active_activity)) ?>
  	<div id="tabs" class="row">
  	  <div class="small-12 columns">
			<ul class="tabs" data-tab>
				<li class="tab-title active"><a href="#panel-tasks">Tasks</a></li>
				<li class="tab-title"><a href="#panel-assets">Assets</a></li>
				<li class="tab-title"><a href="#panel-issues">Issues</a></li>
				<li class="tab-title"><a href="#panel-completed">Your Shift</a></li>
			</ul>
		</div>
  	</div>
  	<div class="row">
  	  <div class="small-11 small-centered columns">
    	  <div class="tabs-content">
	  		
	  		
	  		<!-- Actionable Tasks -->
	  		
	  		
	  		<div class="content active" id="panel-tasks">
    			<div id="task-list-table" class="row">
    			  <div class="small-6 columns">
      			  <h3 class="group-heading">Actionable Tasks</h3>
              <?php
              if($actionable_tasks) { ?>
              <ol class="group">
              <?php
              foreach($actionable_tasks as $task) : ?>
                <div class="group-item<?php if(strtotime($task->task_due_date) < time()) { echo " overdue"; } ?>">
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
            				<p class="lesson-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(); ?></a> <?php echo $task->task_name; ?></p>
            				<p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
          				</div>
                  <div class="actions">
                    <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
            					<input type='hidden' name='task_id' value='<?php echo $task->id; ?>'>
            					<input type='submit' name='task_activated' value='Activate'>
          					</form>
                  </div>
                </div>
              <?php endforeach; ?>
              </ol>
            </div>
            <?php } // end if($actionable_tasks)_ ?>
          </div>
  			</div>
  			
  			
  			<!-- Actionable Assets -->
  			
  			
  			<div class="content" id="panel-assets">
  			  <?php if($actionable_assets) { ?>
  				<div id="actionable-asset-list-table" class="row">
    			  <div class="small-12 medium-6 columns">
      			  <h3 class="group-heading">Actionable Assets</h3>
              <ol class="group">
              <?php
              foreach($actionable_assets as $task) : ?>
                <div class="group-item<?php if(strtotime($task->task_due_date) < time()) { echo " overdue"; } ?>">
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
            				<p class="lesson-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(); ?></a> <?php echo $task->task_name; ?></p>
            				<p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
          				</div>
                  <div class="actions">
                     <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
            					<input type='hidden' name='task_id' value='<?php echo $task->id; ?>'>
            					<input type='submit' name='asset_completed' value='Complete'>
            					<input type='submit' name='asset_completed_and_delivered' value='Complete and Deliver'>
          					</form>
                  </div>
                </div>
              <?php endforeach; ?>
              </ol>
            </div>
          </div>
          <?php } // end if($actionable_tasks)_ ?>
          
          
          <!-- Deliverable Assets -->
          
          
  				<?php if($deliverable_assets) { ?>
  				<div id="deliverable-asset-list-table" class="row">
    			  <div class="small-12 medium-6 columns">
      			  <h3 class="group-heading">Deliverable Assets</h3>
              <ol class="group">
              <?php
              foreach($deliverable_assets as $task) : ?>
                <div class="group-item<?php if(strtotime($task->task_due_date) < time()) { echo " overdue"; } ?>">
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
            				<p class="lesson-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(); ?></a> <?php echo $task->task_name; ?></p>
            				<p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
          				</div>
                  <div class="actions">
                    <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
            					<input type='hidden' name='task_id' value='<?php echo $task->id; ?>'>
            					<input type='submit' name='asset_delivered' value='Deliver'>
            				</form>
                  </div>
                </div>
              <?php endforeach; ?>
              </ol>
            </div>
          </div>
          <?php } // end if($actionable_tasks) ?>
  			</div>
  			
  			
  			<!-- Actionable Issues -->
  			
  			
  			<div class="content" id="panel-issues">
          <div id="pending-issues-list-table" class="row">
            <div class="small-12 columns">
            <?php if($actionable_issues) { ?>
              <?php if(!$working_on_issues) { ?>
                <form method="post" action="task-sheet.php?member=<?php echo $team_member_id; ?>">
              		<input type="submit" class="button" name="start_fixing_issues" value="Start Fixing Issues">
              	</form>
              <?php } else { ?>
                <form method="post" action="task-sheet.php?member=<?php echo $team_member_id; ?>">
              		<input type="submit" class="button" name="stop_fixing_issues" value="Stop Fixing Issues">
              	</form>
              <?php } ?>
        		<h3 class="group-heading">Pending Issues</h3>
            <ol class="group">
            <?php
            foreach($actionable_issues as $issue) : 
              $task = Task::find_by_id($issue->task_id); ?>
              <div class="group-item">
                <div class="member">
                  <div class="member-image">
                    <img src="img/headshot-<?php echo strtolower($issue->team_member_name); ?>.png">
                  </div>
                  <p class="member-name">
            				<?php if($session->is_admin()) {
          				    echo "<a href='task-sheet.php?member={$task->team_member_id}'>{$issue->team_member_name}</a>";  
          				  } else {
            				  echo $issue->team_member_name;
          				  } ?>
                  </p>
        				</div>
        				<div class="issue-info">
          				<p class="lesson-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(); ?></a> <?php echo $task->task_name; ?></p>
          				<p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
        				</div>
        				<div class="issue-content">
        				  <p class="issue-body"><?php echo $issue->issue_body; ?></p>
        				</div>
        				<?php if($working_on_issues) { ?>
                <div class="actions">
                  <form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
                    <input type='hidden' name='issue_id' value='<?php echo $issue->id; ?>'>
                    <input type='submit' name='issue_completed' value='Fixed'>
                  </form>
                </div>
        				<?php } ?>
          		</div>
            <?php endforeach; ?>
            </ol>
            <?php } ?>
          </div>
          </div>
  			</div>
  			
  			
  			<!-- Your Shift -->
  			
  			
  			<div class="content" id="panel-completed">
  				<h3 class="group-heading">Your Shift</h3>
          <div id="current_shift_stats" class="panel">
      			<p>Began: <?php echo $logged_in_user->local_time($active_shift->clock_in); ?></p>
      			<form method="post" action="task-sheet.php?member=<?php echo $team_member_id; ?>">
      			<input type="submit" name="end_shift" value="End Shift">
            </form>
      		</div>
    		</div>
  		</div>
    	</div>
  	</div>
<?php } else { // else not if($active_shift) ?>
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
<?php } // end if($active_shift) ?>

<?php include_layout_template('footer.php'); ?>