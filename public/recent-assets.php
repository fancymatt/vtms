<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$days_past = 1;
	$recent_assets = Task::get_recently_delivered_assets(25);
	$actionable_assets = Task::get_all_actionable_asset_tasks(25);
	$deliverable_assets = Task::get_all_deliverable_asset_tasks();
	$logged_in_user = User::find_by_id($session->user_id);
?>

<?php include_layout_template('header.php'); ?>
		
		<div class="row">
  		<div class="small-12 columns">
  		  <h3>Assets</h3>
  		</div>
		</div>
		
		<?php if($deliverable_assets) { ?>
		<div id="deliverable-assets" class="medium-8 medium-centered small-12 columns">
  		<h3 class="group-heading">Deliverable Assets</h3>
	      <ol class="group">
	      <?php
	      foreach($deliverable_assets as $task) : ?>
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
	    			</div>
	    		</div>
	      <?php endforeach; ?>
	      </ol>
    </div>
    <?php } ?>
		
		<div id="active-assets" class="medium-6 small-12 columns">
  		<h3 class="group-heading">Recent Assets</h3>
      <?php
      if($recent_assets) { ?>
	      <ol class="group">
	      <?php
	      foreach($recent_assets as $task) : ?>
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
	    			</div>
	    		</div>
	      <?php endforeach; ?>
	      </ol>
	    <?php } ?>
    </div>
    
    <div id="actionable-assets" class="medium-6 small-12 columns">
  		<h3 class="group-heading">Actionable Assets</h3>
      <?php
      if($actionable_assets) { ?>
	      <ol class="group">
	      <?php
	      foreach($actionable_assets as $task) : ?>
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
	    				<p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
	  				</div>
	  				<div class="actions">
	  				  <a class="action-item" href="#"><img src="img/icon-add-issue.png"></a>
	  					<a class="action-item" href="#"><img src="img/icon-add-issue.png"></a>
	    			</div>
	    		</div>
	      <?php endforeach; ?>
	      </ol>
	    <?php } ?>
    </div>

<?php include_layout_template('footer.php'); ?>