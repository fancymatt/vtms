<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$days_past = 1;
	$recent_tasks = Task::get_recently_completed_tasks(25);
	$active_tasks = Task::get_all_active_tasks();
	$actionable_tasks = Task::get_all_actionable_tasks(25);
	$logged_in_user = User::find_by_id($session->user_id);
?>

<?php include_layout_template('header.php'); ?>
  
    <div class="row">
  		<div class="small-12 columns">
  		  <h3>Tasks</h3>
  		</div>
		</div>
    <div class="column">
      <?php if($active_tasks) { ?>
      <div id="active-tasks" class="medium-6 small-12 columns">
  		<h3 class="group-heading">Active Tasks</h3>
      <ol class="group">
      <?php
      foreach($active_tasks as $task) : ?>
        <div class="group-item active">
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
    				<p class="lesson-title"><?php echo $task->display_full_task_lesson(); ?></p>
    				<p class="task-title"><?php echo $task->task_name; ?></p>
    				<p class="date"><?php echo "Activated ".$logged_in_user->local_time($task->activated_time); ?></p>
  				</div>
  				<div class="actions">
  				  <a class="action-item" href="#"><img src="img/icon-add-issue.png"></a>
  					<a class="action-item" href="#"><img src="img/icon-add-issue.png"></a>
  				</ul>
    			</div>
    		</div>
      <?php endforeach; ?>
      </ol>
		</div>
  		<?php } ?>
  		<div id="actionable-tasks" class="medium-6 small-12 columns">
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
    				<p class="lesson-title"><?php echo $task->display_full_task_lesson(); ?></p>
    				<p class="task-title"><?php echo $task->task_name; ?></p>
    				<p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
  				</div>
          <div class="actions">
  					<a class="action-item" href="#"><img src="img/icon-add-issue.png"></a>
  				</ul>
          </div>
  			</div>
      <?php endforeach; ?>
      </ol>
      <?php } ?>
		</div>
    </div>
  	<div class="column">
  	  <div id="recent-tasks" class="medium-6 small-12 columns">
  		<h3 class="group-heading">Recent Completed Tasks</h3>
      <?php
      if($recent_tasks) { ?>
      <ol class="group">
      <?php
      foreach($recent_tasks as $task) : ?>
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
    				<p class="lesson-title"><?php echo $task->display_full_task_lesson(); ?></p>
    				<p class="task-title"><?php echo $task->task_name; ?></p>
    				<p class="date"><?php echo "Completed ".$logged_in_user->local_time($task->completed_time); ?></p>
  				</div>
  				<div class="actions">
  				  <a class="action-item" href="#"><img src="img/icon-add-issue.png"></a>
  					<a class="action-item" href="#"><img src="img/icon-add-issue.png"></a>
  				</ul>
    			</div>
    		</div>
      <?php endforeach; ?>
      </ol>
      <?php } ?>
      </div>	
		</div>
		

<?php include_layout_template('footer.php'); ?>