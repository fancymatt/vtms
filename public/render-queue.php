<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	$current_time = new DateTime(null, new DateTimeZone('UTC'));
	$logged_in_user = User::find_by_id($session->user_id);
	
	if(isset($_POST['add_lesson_to_queue'])) {
		$lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($lesson_id);
		$lesson->is_queued = 1;
		$lesson->queued_time = $current_time->format('Y-m-d H:i:s');
		$lesson->update();
	}
	
	if(isset($_POST['mark_lesson_as_exported'])) {
		// and update qa fields
		$lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($lesson_id);
		$lesson->is_queued = 0;
		$lesson->exported_time = $current_time->format('Y-m-d H:i:s');
		$lesson->update();
	}
	
	if(isset($_POST['mark_lesson_as_exported_and_updated'])) {
		// and update qa fields
		$lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($lesson_id);
		$lesson->is_queued = 0;
		$lesson->qa_log = $db->escape_value($_POST['qa_log']);
		$lesson->qa_url = $db->escape_value($_POST['qa_url']);
		$lesson->exported_time = $current_time->format('Y-m-d H:i:s');
		$lesson->update();
	}
	
	if(isset($_POST['unqueue_lesson'])) {
		// and update qa fields
		$lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($lesson_id);
		$lesson->is_queued = 0;
		$lesson->exported_time = '';
		$lesson->update();
	}
  
  $render_thresholds = Series::generate_render_threshold_array();
  
	$exportable_lessons = Lesson::find_all_exportable_lessons();
	$queued_lessons = Lesson::find_all_queued_lessons();
?>

<?php include_layout_template('header.php'); ?>

	<div id="breadcrumbs" class="row">
		<ul class="breadcrumbs">
		  <li><a href="operations.php">Operations</a></li>
			<li class="current"><a href="operations.php">Render Queue</a></li>
		</ul>
	</div>

  <?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>

<div class="row">
    <div id="admin-qa" class="small-12 columns">
	<div id="export-these">
		<h5>Export These</h3>
		<table>
			<tr><th>Lesson</th><th>Actions</th><th>Last Action</th><th>Action Time</th><th>Last Render</th><th>Due Date</th></tr>
				<?php 
				if(!$exportable_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($exportable_lessons as $lesson) {
  					$last_issue = Issue::find_last_fixed_issue_for_lesson($lesson->id);
  					
  					if($lesson->comp_value >= $render_thresholds[$lesson->series_id] && $lesson->pending_issues() < 1 ) {
    					
    					$last_task = Task::find_last_task_for_lesson($lesson->id);
              $last_issue = Issue::find_last_fixed_issue_for_lesson($lesson->id);
    					
      				if($last_task->completed_time > $lesson->exported_time) {
        				// Task finished after last export
        				
      					$team_member = Member::find_by_id($last_task->team_member_id);
      					
      				  echo "<tr>";
    						echo "<td>";
    						echo $lesson->display_full_lesson_with_link();
    						echo "</td>";
    						echo "<td><form action='render-queue.php' method='post'>";
    						echo "<input type='hidden' name='qa_lesson_id' value='{$lesson->id}'><input type='submit' name='add_lesson_to_queue' value='Add To Queue'></form></td>";
    						echo "<td>";
    						echo $team_member->first_name;
    						echo " - ";
    						echo $last_task->task_name;
    						echo "</td>";
    						echo "<td>";
                echo date("M jS g:i a", strtotime($logged_in_user->local_time($last_task->completed_time))); 
                echo "</td>";
    						echo "<td>";
    						if($lesson->exported_time > 0) {
      						echo date("M jS g:i a", strtotime($logged_in_user->local_time($lesson->exported_time)));
    						} else {
      						echo "Never";
    						}
    						echo "</td>";
    						echo "<td>";
    						echo date("M jS Y", strtotime($logged_in_user->local_time($lesson->publish_date)));
    						echo "</td>";
    						echo "</tr>";	
    					}
    					
    					if($last_issue->time_completed > $lesson->exported_time) {
        				// Issue finished after last export
        				
      					$team_member = Member::find_by_id($last_issue->team_member_id);
      					
      				  echo "<tr>";
    						echo "<td>";
    						echo $lesson->display_full_lesson_with_link();
    						echo "</td>";
    						echo "<td><form action='render-queue.php' method='post'>";
    						echo "<input type='hidden' name='qa_lesson_id' value='{$lesson->id}'><input type='submit' name='add_lesson_to_queue' value='Add To Queue'></form></td>";
    						echo "<td>";
    						echo $team_member->first_name;
    						echo " - Issue Fixed";;
    						echo "</td>";
    						echo "<td>";
                echo date("M jS g:i a", strtotime($logged_in_user->local_time($last_issue->completed_time))); 
                echo "</td>";
    						echo "<td>";
    						if($lesson->exported_time > 0) {
      						echo date("M jS g:i a", strtotime($logged_in_user->local_time($lesson->exported_time)));
    						} else {
      						echo "Never";
    						}
    						echo "</td>";
    						echo "<td>";
    						echo date("M jS Y", strtotime($logged_in_user->local_time($lesson->publish_date)));
    						echo "</td>";
    						echo "</tr>";	
    					}
    					
  					}
					} 
				 } ?>		
		</table>
	</div>
		
	<div id="export-queue">
		<h5>Export Queue</h3>
		<table>
			<tr><th>Lesson</th><th>Queued Time</th><th>Actions</th></tr>
				<?php 
				if(!$queued_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($queued_lessons as $lesson) {
						echo "<tr>";
						echo "<td>";
						echo $lesson->display_full_lesson();
						echo "</td>";
						echo "<td>".$logged_in_user->local_time($lesson->queued_time)."</td>";
						echo "<td><form action='render-queue.php' method='post'>";
						if($lesson->checked_language) {
							echo "<input type='hidden' name='qa_lesson_id' value='{$lesson->id}'><input type='submit' name='mark_lesson_as_exported' value='Exported'></form>";
						} else {
							echo "Log: <input type='text' size=60 name='qa_log' value='{$lesson->qa_log}'><br />";
							echo "URL: <input type='text' size=60 name='qa_url' value='{$lesson->qa_url}'>";
							echo "<input type='hidden' name='qa_lesson_id' value='{$lesson->id}'><input type='submit' name='mark_lesson_as_exported_and_updated' value='Exported & Updated'></form>";
						}
						echo "<form action='render-queue.php' method='post'><input type='hidden' name='qa_lesson_id' value='{$lesson->id}'><input type='submit' name='unqueue_lesson' value='Unqueue!'></form>";
						echo "</tr>";
					} 
				 } ?>		
		</table>
	</div>
	<p><a href="operations.php"><- Return to Operations page</a></p>

<?php include_layout_template('footer.php'); ?>