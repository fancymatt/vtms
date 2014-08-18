<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$days_past = 1;
	$tasks_recent = Task::get_recently_completed_tasks(10);
	$tasks_active = Task::get_all_active_tasks();
	$tasks_actionable = Task::get_all_actionable_tasks(10);
	$logged_in_user = User::find_by_id($session->user_id);
?>

<?php include_layout_template('header.php'); ?>

<div id="tasks-actionable" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Active Tasks</h3>
    <div class="group-item-sort-options">
    </div>
  </div>
  <?php if($tasks_active) { ?>
  <?php
  foreach($tasks_active as $task) : 
  ?>
  <div class="group-item<?php if(strtotime($task->task_due_date) < time()) { echo " overdue"; } ?>">
    <div class="group-item-body">
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
      <div class="group-item-header">
        <h3 class="group-item-title"><a href="#"><?php echo $task->display_full_task_lesson(). "</a> ".$task->task_name; ?></h3>
      </div>
      <div class="group-item-content">
        <div class="group-item-metadata">
          <p>Activated <?php echo date("g:i a", strtotime($logged_in_user->local_time($task->activated_time))); ?></p>
        </div>
        <div class="group-item-text">
        </div>
        <div class="group-item-actions">
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php } ?>
</div>

<div id="tasks-recent" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Recently Completed Tasks</h3>
    <div class="group-item-sort-options">
    </div>
  </div>
<?php if($tasks_recent) { ?>
  <?php
  foreach($tasks_recent as $task) : 
  ?>
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
    <div class="group-item-body">
      <div class="group-item-header">
        <h3 class="group-item-title"><a href="#"><?php echo $task->display_full_task_lesson(). "</a> ".$task->task_name; ?></h3>
      </div>
      <div class="group-item-content">
        <div class="group-item-metadata">
          <p><?php echo date("M jS, g:i a", strtotime($logged_in_user->local_time($task->completed_time))); ?></p>
          <p>Duration: <?php echo seconds_to_timecode($task->time_actual, 6); ?></p>
        </div>
        <div class="group-item-text">
        </div>
        <div class="group-item-actions">
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php } ?>
</div>

<div id="tasks-actionable" class="small-12 medium-8 medium-centered columns">
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
    <div class="group-item-body">
      <div class="group-item-header">
        <h3 class="group-item-title"><a href="#"><?php echo $task->display_full_task_lesson(). "</a> ".$task->task_name; ?></h3>
      </div>
      <div class="group-item-content">
        <div class="group-item-metadata">
          <p><?php echo "Due ".$task->task_due_date; ?></p>
        </div>
        <div class="group-item-text">
        </div>
        <div class="group-item-actions">
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php } ?>
</div>
<br />

<?php include_layout_template('footer.php'); ?>