<?php require_once("../includes/initialize.php"); ?>
<?php
  $logged_in_user = User::find_by_id($session->user_id);
	$shifts = Shift::find_all_recent_shifts();
	
	$issues_actionable = Issue::get_all_unfinished_issues(5);
	$issues_actionable_for_member = Issue::get_unfinished_issues_for_member(3);
	$issues_completed = Issue::get_recently_completed_issues(1);
	
	$tasks_active = Task::get_all_active_tasks();
  $tasks_active_for_member = Task::get_active_tasks_for_member(3);
	$tasks_actionable = Task::get_all_actionable_tasks(5);
	$tasks_actionable_for_member = Task::get_actionable_tasks_for_member(3);
	$tasks_for_lesson = Task::find_all_tasks_for_lesson(6406);
	$tasks_completed = Task::get_recently_completed_tasks(5);
	
	$assets_actionable = Task::get_all_actionable_asset_tasks(5);
	$assets_actionable_for_member = Task::get_actionable_assets_for_member(21);
	$assets_for_lesson = Task::find_all_assets_for_lesson(6406);
	$assets_deliverable = Task::get_all_deliverable_asset_tasks();
	$assets_deliverable_for_member = Task::get_deliverable_assets_for_member(21);
	$assets_completed = Task::get_recently_delivered_assets(5);
	
	//$lessons_due_soon = Lesson::find_all_upcoming_due_lessons(1);
	//$lessons_checkable = Lesson::find_all_ready_to_video_check_lessons();
	//$lessons_in_queue = Lesson::find_all_queued_lessons();
	//$lessons_exportable = Lesson::find_all_exportable_lessons();
	//$lessons_in_language_series = Lesson::find_all_lessons_for_language_series(7);
	
?>

<?php include_layout_template('header.php'); ?>


<!-- Issues -->


<div id="DIVTITLE" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Recently Completed Issues</h3>
    <div class="group-item-sort-options">
      Sort by: 
      <a class="sort-option" href="#">OPTION</a>
      <a class="sort-option" href="#">OPTION</a>
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
      Sort by: 
      <a class="sort-option" href="#">OPTION</a>
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
          <p><?php echo $issue->issue_body; ?></p>
        </div>
        <div class="group-item-actions">
          <form action='recent-issues.php' method='post'>
            <input type='hidden' name='issue_id' value='<?php echo $issue->id; ?>'>
            <input type='submit' class="action button" name='issue_completed' value='Fixed'>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php } ?>
</div>

<!-- Tasks -->

<div id="tasks-actionable" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Actionable Tasks</h3>
    <div class="group-item-sort-options">
      Sort by: 
      <a class="sort-option" href="#">OPTION</a>
    </div>
  </div>
<?php if($tasks_actionable_for_member) { ?>
  <?php
  foreach($tasks_actionable_for_member as $task) : 
  ?>
  <div class="group-item<?php if(strtotime($task->task_due_date) < time()) { echo " overdue"; } ?>">
    <div class="group-item-body">
      <div class="group-item-header">
        <h3 class="group-item-title"><a href="#"><?php echo $task->display_full_task_lesson(). "</a> ".$task->task_name; ?></h3>
      </div>
      <div class="group-item-content">
        <div class="group-item-metadata">
          <p>Due <?php echo $task->task_due_date; ?></p>
        </div>
        <div class="group-item-text">
        </div>
        <div class="group-item-actions">
          <form action='recent-issues.php' method='post'>
            <input type='hidden' name='issue_id' value='<?php echo $issue->id; ?>'>
            <input type='submit' class="action button" name='issue_completed' value='Activate'>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php } ?>
</div>

<div id="tasks-actionable" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Actionable Tasks (for all)</h3>
    <div class="group-item-sort-options">
      Sort by: 
      <a class="sort-option" href="#">OPTION</a>
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
          <p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
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
    <h3 class="group-title">Tasks for Lesson</h3>
    <div class="group-item-sort-options">
      Sort by: 
      <a class="sort-option" href="#">OPTION</a>
    </div>
  </div>
<?php if($tasks_for_lesson) { ?>
  <?php
  foreach($tasks_for_lesson as $task) : 
  ?>
  <div class="group-item<?php if($task->is_completed) { echo " ready"; } ?>">
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
        <h3 class="group-item-title"><a href="#"><?php echo $task->task_name; ?></h3>
      </div>
      <div class="group-item-content">
        <div class="group-item-metadata">
          <p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
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

<!-- Assets -->

<div id="tasks-actionable" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Actionable Tasks</h3>
    <div class="group-item-sort-options">
      Sort by: 
      <a class="sort-option" href="#">OPTION</a>
    </div>
  </div>
<?php if($assets_actionable_for_member) { ?>
  <?php
  foreach($assets_actionable_for_member as $task) : 
  ?>
  <div class="group-item<?php if(strtotime($task->task_due_date) < time()) { echo " overdue"; } ?>">
    <div class="group-item-body">
      <div class="group-item-header">
        <h3 class="group-item-title"><a href="#"><?php echo $task->display_full_task_lesson(). "</a> ".$task->task_name; ?></h3>
      </div>
      <div class="group-item-content">
        <div class="group-item-metadata">
          <p>Due <?php echo $task->task_due_date; ?></p>
        </div>
        <div class="group-item-text">
        </div>
        <div class="group-item-actions">
          <form action='recent-issues.php' method='post'>
            <input type='hidden' name='issue_id' value='<?php echo $issue->id; ?>'>
            <input type='submit' class="action button" name='issue_completed' value='Complete'>
          </form>
          <form action='recent-issues.php' method='post'>
            <input type='hidden' name='issue_id' value='<?php echo $issue->id; ?>'>
            <input type='submit' class="action button" name='issue_completed' value='Deliver'>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php } ?>
</div>

<div id="tasks-actionable" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Actionable Assets (for all)</h3>
    <div class="group-item-sort-options">
      Sort by: 
      <a class="sort-option" href="#">OPTION</a>
    </div>
  </div>
<?php if($assets_actionable) { ?>
  <?php
  foreach($assets_actionable as $task) : 
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
          <p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
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
    <h3 class="group-title">Deliverable Assets (for member)</h3>
    <div class="group-item-sort-options">
      Sort by: 
      <a class="sort-option" href="#">OPTION</a>
    </div>
  </div>
<?php if($assets_deliverable_for_member) { ?>
  <?php
  foreach($assets_deliverable_for_member as $task) : 
  ?>
  <div class="group-item<?php if(strtotime($task->task_due_date) < time()) { echo " overdue"; } ?>">
    <div class="group-item-body">
      <div class="group-item-header">
        <h3 class="group-item-title"><a href="#"><?php echo $task->display_full_task_lesson(). "</a> ".$task->task_name; ?></h3>
      </div>
      <div class="group-item-content">
        <div class="group-item-metadata">
          <p>Due <?php echo $task->task_due_date; ?></p>
        </div>
        <div class="group-item-text">
        </div>
        <div class="group-item-actions">
          <form action='recent-issues.php' method='post'>
            <input type='hidden' name='issue_id' value='<?php echo $issue->id; ?>'>
            <input type='submit' class="action button" name='issue_completed' value='Deliver'>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php } ?>
</div>

<div id="tasks-actionable" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Deliverable Assets (for all)</h3>
    <div class="group-item-sort-options">
      Sort by: 
      <a class="sort-option" href="#">OPTION</a>
    </div>
  </div>
<?php if($assets_deliverable) { ?>
  <?php
  foreach($assets_deliverable as $task) : 
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
          <p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
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
    <h3 class="group-title">Recently Delivered Assets</h3>
    <div class="group-item-sort-options">
      Sort by: 
      <a class="sort-option" href="#">OPTION</a>
    </div>
  </div>
<?php if($assets_completed) { ?>
  <?php
  foreach($assets_completed as $task) : 
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
          <p class="date"><?php echo $logged_in_user->local_time($task->completed_time); ?></p>
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

<p>Up to this part</p>

<div id="tasks-actionable" class="small-12 medium-8 medium-centered columns">
  <div class="group-header">
    <h3 class="group-title">Assets for Lesson</h3>
    <div class="group-item-sort-options">
      Sort by: 
      <a class="sort-option" href="#">OPTION</a>
    </div>
  </div>
<?php if($assets_for_lesson) { ?>
  <?php
  foreach($assets_for_lesson as $task) : 
  ?>
  <div class="group-item<?php if($task->is_completed) { echo " ready"; } ?>">
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
        <h3 class="group-item-title"><a href="#"><?php echo $task->task_name; ?></h3>
      </div>
      <div class="group-item-content">
        <div class="group-item-metadata">
          <p class="date"><?php echo "Due ".$task->task_due_date; ?></p>
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

<p>Under construction</p>

<!-- Lessons -->

<div class="small-12 medium-8 medium-centered columns">
  <h3>Upcoming Lessons</h3>
</div>
<?php if($lessons_due_soon) { ?>
<div id="section" class="small-12 medium-8 medium-centered columns">
  <h3 class="group-heading">Group Heading</h3>
  <ol class="group">
    <?php foreach($lessons_due_soon as $lesson) : ?>
    <div class="group-item">
    </div>
    <?php endforeach; ?>
  </ol>
</div>
<?php } // end if(lessons_due_soon) ?>

<div class="small-12 medium-8 medium-centered columns">
  <h3>Queued Lessons</h3>
</div>
<?php if($lessons_in_queue) { ?>
<div id="section" class="small-12 medium-8 medium-centered columns">
  <h3 class="group-heading">Group Heading</h3>
  <ol class="group">
    <?php foreach($lessons_in_queue as $lesson) : ?>
    <div class="group-item">
    </div>
    <?php endforeach; ?>
  </ol>
</div>
<?php } // end if($lessons_in_queue) ?>

<div class="small-12 medium-8 medium-centered columns">
  <h3>Exportable Lessons</h3>
</div>
<?php if($lessons_exportable) { ?>
<div id="section" class="small-12 medium-8 medium-centered columns">
  <h3 class="group-heading">Group Heading</h3>
  <ol class="group">
    <?php foreach($lessons_exportable as $lesson) : ?>
    <div class="group-item">
    </div>
    <?php endforeach; ?>
  </ol>
</div>
<?php } // end if($lessons_exportable) ?>

<div class="small-12 medium-8 medium-centered columns">
  <h3>QA Lessons</h3>
</div>
<?php if($lessons_checkable) { ?>
<div id="section" class="small-12 medium-8 medium-centered columns">
  <h3 class="group-heading">Group Heading</h3>
  <ol class="group">
    <?php foreach($lessons_checkable as $lesson) : ?>
    <div class="group-item">
    </div>
    <?php endforeach; ?>
  </ol>
</div>
<?php } // end if($lessons_checkable) ?>

<div class="small-12 medium-8 medium-centered columns">
  <h3>Admin QA Lessons</h3>
</div>
<?php if($lessons_checkable) { ?>
<div id="section" class="small-12 medium-8 medium-centered columns">
  <h3 class="group-heading">Group Heading</h3>
  <ol class="group">
    <?php foreach($lessons_checkable as $lesson) : ?>
    <div class="group-item<?php if (strtotime($lesson->publish_date) < time()) { 
                                      echo " overdue"; 
                                      } else if (strpos(strtolower($lesson->qa_log), "approved") !== false) {
                                      echo " ready";
                                      } ?>">
	    <div class="lesson-info">
				<a class="lesson-title" href="lesson.php?id=<?php echo $lesson->id; ?>"><?php echo $lesson->display_full_lesson(); ?></a>
				<div class="qa-status">
				  <div class="small-2 columns">
				    <form action='operations.php' method='post'>
  				  <label for="qa-log" class="inline">QA Log: </label>
  				  <label for="qa-url" class="inline">QA URL: </label>
				  </div>
				  <div class="small-10 columns">
  				  <input type="text" name="qa_log" size=40 value="<?php echo $lesson->qa_log; ?>">
            <input type="text" name="qa_url" size=40 value="<?php echo $lesson->qa_url; ?>">
				  </div>
				</div>
				<p class="date"><?php echo "Due ".$lesson->publish_date; ?></p>
				<p class="date"><?php echo "Exported ".$lesson->exported_time; ?></p>
			</div>
			<div class="actions">
				<li class="action-item">
					<input type='hidden' name='qa_lesson_id' value='<?php echo $lesson->id; ?>'>
          <input type="submit" value="Update QA" class="no-format" name="changed_qa" data-tooltip class="has-tip" title="Update QA Log">
					</form>
				</li>
				<li class="action-item">
				  <form action='operations.php' method='post'>
            <input type='hidden' name='qa_lesson_id' value='<?php echo $lesson->id; ?>'>
            <input type='submit' class="no-format" name='marked_lesson_language_checked' value='Mark Language Checked' data-tooltip class="has-tip" title="Mark as Language Checked"></form>
				</li>
			</div>
    </div>
  <?php endforeach; ?>
  </ol>
  <?php } else { ?>
    <div class="group-item">
      <div class="lesson-info">
        <a class="lesson-title" href="#">No lessons waiting on language check.</a>
      </div>
    </div>
  </ol>
</div>
<?php } // end if($lessons_checkable) ?>

<div class="small-12 medium-8 medium-centered columns">
  <h3>Lessons</h3>
</div>
<?php if($lessons_in_language_series) { ?>
<div id="section" class="small-12 medium-8 medium-centered columns">
  <h3 class="group-heading">Group Heading</h3>
  <ol class="group">
    <?php foreach($lessons_in_language_series as $lesson) : ?>
    <div id="admin-qa" class="small-12 columns">
  		<h3 class="group-heading">Waiting for Language Check</h3>
      <?php
      if($qa_lessons) { ?>
      <ol class="group">
      <?php
      foreach($lessons_checkable as $lesson) : ?>
        <div class="group-item<?php if (strtotime($lesson->publish_date) < time()) { 
                                      echo " overdue"; 
                                      } else if (strpos(strtolower($lesson->qa_log), "approved") !== false) {
                                      echo " ready";
                                      } ?>">
  		    <div class="lesson-info">
    				<a class="lesson-title" href="lesson.php?id=<?php echo $lesson->id; ?>"><?php echo $lesson->display_full_lesson(); ?></a>
    				<div class="qa-status">
    				  <div class="small-2 columns">
    				    <form action='operations.php' method='post'>
      				  <label for="qa-log" class="inline">QA Log: </label>
      				  <label for="qa-url" class="inline">QA URL: </label>
    				  </div>
    				  <div class="small-10 columns">
      				  <input type="text" name="qa_log" size=40 value="<?php echo $lesson->qa_log; ?>">
                <input type="text" name="qa_url" size=40 value="<?php echo $lesson->qa_url; ?>">
    				  </div>
    				</div>
    				<p class="date"><?php echo "Due ".$lesson->publish_date; ?></p>
    				<p class="date"><?php echo "Exported ".$lesson->exported_time; ?></p>
  				</div>
  				<div class="actions">
  					<li class="action-item">
							<input type='hidden' name='qa_lesson_id' value='<?php echo $lesson->id; ?>'>
              <input type="submit" value="Update QA" class="no-format" name="changed_qa" data-tooltip class="has-tip" title="Update QA Log">
  						</form>
  					</li>
  					<li class="action-item">
  					  <form action='operations.php' method='post'>
                <input type='hidden' name='qa_lesson_id' value='<?php echo $lesson->id; ?>'>
                <input type='submit' class="no-format" name='marked_lesson_language_checked' value='Mark Language Checked' data-tooltip class="has-tip" title="Mark as Language Checked"></form>
  					</li>
    			</div>
        </div>
      <?php endforeach; ?>
      </ol>
      <?php } else { ?>
        <div class="group-item">
          <div class="lesson-info">
            <a class="lesson-title" href="#">No lessons waiting on language check.</a>
          </div>
        </div>
      <?php } ?>
    </div>
    <?php endforeach; ?>
  </ol>
</div>
<?php } // end if($lessons_in_language_series) ?>



<!-- Language Series -->


<!-- Series -->


<!-- Sessions -->


    
<?php include_layout_template('footer.php'); ?>