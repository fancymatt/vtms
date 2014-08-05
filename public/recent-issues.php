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
	$recent_issues = Issue::get_recently_completed_issues($days_past);
	$actionable_issues = Issue::get_all_unfinished_issues();
	$logged_in_user = User::find_by_id($session->user_id);
?>

<?php include_layout_template('header.php'); ?>

	<?php 
	if($message) { ?>
  <div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
		
		<div class="row">
  		<div class="small-12 medium-8 medium-centered columns">
  		  <h3>Issues</h3>
  		</div>
		</div>
		<?php if($recent_issues) { ?>
		<div id="recent-issues" class="small-12 medium-8 medium-centered columns">
  		<h3 class="group-heading">Recently Fixed Issues</h3>
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
    				<p class="lesson-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(); ?></a> <?php echo $task->task_name; ?></p>
    				<p class="date"><?php echo "Completed ".$logged_in_user->local_time($issue->time_completed); ?></p>
  				</div>
  				<div class="issue-content">
  				  <p class="issue-body"><?php echo $issue->issue_body; ?></p>
    			</div>
    		</div>
      <?php endforeach; ?>
      </ol>
    </div>
    <?php } ?>
    
    <div id="pending-issues" class="small-12 medium-8 medium-centered columns">
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
    				<p class="lesson-title"><a href="lesson.php?id=<?php echo $task->lesson_id; ?>"><?php echo $task->display_full_task_lesson(); ?></a> <?php echo $task->task_name; ?></p>
  				<p class="date"><?php echo "Reported by ".$issue->issue_creator; ?></p>
          <p class="date"><?php if($issue->issue_timecode) { 
                              echo $issue->issue_timecode;
                            } else {
                              echo "No timecode.";
                            } ?></p>
  				</div>
  				<div class="issue-content">
  				  <p class="issue-body"><?php echo $issue->issue_body; ?></p>
  				</div>
      		<?php if($session->is_admin()) { ?>
          <div class="actions">
            <form action='recent-issues.php' method='post'>
              <input type='hidden' name='issue_id' value='<?php echo $issue->id; ?>'>
              <input type='submit' name='issue_completed' value='Fixed'>
            </form>
          </div>
        </div>
				<?php } ?>
      <?php endforeach; ?>
      </ol>
      <?php } ?>
    </div>
    
<?php include_layout_template('footer.php'); ?>