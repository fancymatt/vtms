<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$days_past = 1;
	$assets_recent = Task::get_recently_shot_lessons(25);
	$logged_in_user = User::find_by_id($session->user_id);
?>

<?php include_layout_template('header.php'); ?>

<div id="tasks-actionable" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Recently Shot lessons</h3>
    <div class="group-item-sort-options">
    </div>
  </div>
<?php if($assets_recent) { ?>
  <?php
  foreach($assets_recent as $task) : 
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
          <p class="date">
            <?php 
            echo "Shot ";
            echo date("M jS g:i a", strtotime($logged_in_user->local_time($task->completed_time)));
              
            ?></p>
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

<?php include_layout_template('footer.php'); ?>