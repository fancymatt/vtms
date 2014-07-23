<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$days_past = 1;
	$recent_issues = Issue::get_recently_completed_issues($days_past);
	$actionable_issues = Issue::get_all_unfinished_issues();
	$logged_in_user = User::find_by_id($session->user_id);
?>

<?php include_layout_template('header.php'); ?>
		
		<div class="row">
  		<div class="small-12 columns">
  		  <h3>Issues</h3>
  		</div>
		</div>
		
		<div id="recent-issues" class="small-12 columns">
  		<h3 class="group-heading">Recently Fixed Issues</h3>
      <?php
      if($recent_issues) { ?>
      <ol class="group">
      <?php
      foreach($recent_issues as $issue) : 
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
    				<p class="lesson-title"><?php echo $task->display_full_task_lesson(); ?></p>
    				<p class="task-title"><?php echo $task->task_name; ?></p>
    				<p class="date"><?php echo "Completed ".$logged_in_user->local_time($issue->time_completed); ?></p>
  				</div>
  				<div class="issue-content">
  				  <p class="issue-body"><?php echo $issue->issue_body; ?></p>
    			</div>
    		</div>
      <?php endforeach; ?>
      </ol>
      <?php } ?>
    </div>
    
    <div id="pending-issues" class="small-12 columns">
  		<h3 class="group-heading">Pending Issues</h3>
      <?php
      if($actionable_issues) { ?>
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
    				<p class="lesson-title"><?php echo $task->display_full_task_lesson(); ?></p>
    				<p class="task-title"><?php echo $task->task_name; ?></p>
    				<p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
  				</div>
  				<div class="issue-content">
  				  <p class="issue-body"><?php echo $issue->issue_body; ?></p>
  				</div>
    		</div>
      <?php endforeach; ?>
      </ol>
      <?php } ?>
    </div>
<?php include_layout_template('footer.php'); ?>