<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	
  if($_POST['issue_completed']) {
		$completed_issue_id = $_POST['issue_id'];
		$completed_issue = Issue::find_by_id($completed_issue_id);
		$completed_issue->complete_issue();
		$_SESSION['message'] = "The issue: ".$completed_issue->issue_body." has been marked as complete.";
		redirect_to("recent-issues.php");
	}
	
	$days_past = 1;
	$issues_recent = Issue::get_recently_completed_issues($days_past);
	$issues_actionable = Issue::get_all_unfinished_issues();
	$logged_in_user = User::find_by_id($session->user_id);
?>

<?php include_layout_template('header.php'); ?>

<div id="DIVTITLE" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Recently Completed Issues</h3>
    <div class="group-item-sort-options">
    </div>
  </div>
<?php if($issues_completed) { ?>
  <?php
  foreach($issues_completed as $issue) :
  $task = Task::find_by_id($issue->task_id); 
  ?>
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
    <div class="group-item-body">
      <div class="group-item-header">
        <h3 class="group-item-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(); ?></a> <?php echo $task->task_name; ?></h3>
      </div>
      <div class="group-item-content">
        <div class="group-item-metadata">
          <p>Completed: <?php echo $logged_in_user->local_time($issue->time_completed); ?></p>
        </div>
        <div class="group-item-text">
          <p><?php echo $issue->issue_body; ?></p>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php } ?>
</div>

<div id="issues-actionable" class="small-12 medium-8 medium-centered columns">
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
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php } ?>
</div>
<br />
    
<?php include_layout_template('footer.php'); ?>