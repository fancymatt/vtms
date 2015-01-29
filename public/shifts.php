<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$shifts = Shift::find_all_recent_shifts(25);
	$logged_in_user = User::find_by_id($session->user_id);
?>

<?php include_layout_template('header.php'); ?>
		
		<div class="row">
  		<div class="small-12 columns">
  		  <h3>Shifts</h3>
  		</div>
		</div>
		<?php if($shifts) { ?>
		<div id="shifts" class="small-12 columns">
  		<h3 class="group-heading">Recent Shifts</h3>
      <ol class="group">
      <?php
      foreach($shifts as $shift) :
      $member = Member::find_by_id($shift->team_member_id);
      $activities = Activity::find_all_activities_for_shift($shift->id); ?>
        <div class="group-item">
          <div class="member">
            <div class="member-image">
              <img src="img/headshot-<?php echo strtolower($member->first_name); ?>.png">
            </div>
            <p class="member-name">
      				<?php if($session->is_admin()) {
    				    echo "<a href='task-sheet.php?member={$member->id}'>{$member->first_name}</a>";  
    				  } else {
      				  echo $member->first_name;
    				  } ?>
            </p>
  				</div>
  				<div class="issue-info">
    				<p class="lesson-title"><?php 
    				echo date("M jS", strtotime($logged_in_user->local_time($shift->clock_in)))." ";
    				echo date("g:i a", strtotime($logged_in_user->local_time($shift->clock_in)))." - ";
    				if($shift->is_active) {
      				echo "now";
    				} else {
      				echo date("g:i a", strtotime($logged_in_user->local_time($shift->clock_out)));
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
      <?php endforeach; ?>
      </ol>
    </div>
    <?php } ?>
    
<?php include_layout_template('footer.php'); ?>