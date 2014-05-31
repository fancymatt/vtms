<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$team_member_id = $db->escape_value($_GET['member']);
	$team_member = Member::find_by_id($team_member_id);
	
	if (!$session->is_admin()) {
		// then you'd better be this user
		if ($_SESSION['user_id'] != $team_member->user_id) {
			redirect_to('login.php');
		}
	}
	
	/*
if(!is_object($team_member)) {
		redirect_to("lesson-db.php");
	}
*/

	if($_POST['task_activated']) {
		$activated_task_id = $_POST['task_id'];
		$activated_task = Task::find_by_id($activated_task_id);
		$activated_task->activate_task();
		$activated_global_task = GlobalTask::find_by_id($activated_task->global_task_id);
		$message = $activated_task_id . " has been activated.";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['task_deactivated']) {
		$deactivated_task_id = $_POST['task_id'];
		$deactivated_task = Task::find_by_id($deactivated_task_id);
		$message = $deactivated_task->deactivate_task();
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['task_completed']) {
		$completed_task_id = $_POST['task_id'];
		$completed_task = Task::find_by_id($completed_task_id);
		$message = $completed_task->complete_task() . "<br />";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['asset_delivered']) {
		$completed_task_id = $_POST['task_id'];
		$completed_task = Task::find_by_id($completed_task_id);
		$completed_task->deliver_asset() . "<br />";
		$message = "Asset Delivered";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['asset_completed']) {
		$completed_task_id = $_POST['task_id'];
		$completed_task = Task::find_by_id($completed_task_id);
		$completed_task->complete_asset() . "<br />";
		$message = "Asset Completed";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['asset_completed_and_delivered']) {
		$completed_task_id = $_POST['task_id'];
		$completed_task = Task::find_by_id($completed_task_id);
		$completed_task->complete_and_deliver_asset() . "<br />";
		$message = "Asset Completed and Delivered";
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($_POST['issue_completed']) {
		$completed_issue_id = $_POST['issue_id'];
		$completed_issue = Issue::find_by_id($completed_issue_id);
		$completed_issue->complete_issue();
		redirect_to("task-sheet.php?member={$team_member_id}");
	}
	
	if($team_member_id) {
		$actionable_tasks = Task::get_actionable_tasks_for_member($team_member_id);
		$active_tasks = Task::get_active_tasks_for_member($team_member_id);
		$unresolved_issues = Issue::get_unfinished_issues_for_member($team_member->id);
		$actionable_assets = Task::get_actionable_assets_for_member($team_member->id);
		$deliverable_assets = Task::get_deliverable_assets_for_member($team_member->id);
		
	} else {
		$message = "Could not find user";
	}
	
	
?>
<?php include_layout_template('header.php'); ?>
<div>
<h2 id="main_title"><?php echo $team_member->first_name; ?>'s Task Sheet</h2>
<?php if($message) { echo "<p>{$message}</p>"; } ?>
<?php  if($activated_global_task->tutorial_yt_url != '') {
	echo "<div class='panel'>";
	echo "<h3>".$activated_global_task->series_name.": ".$activated_global_task->task_name."</h3>";
	echo "<iframe width='380' height='250' src='//www.youtube.com/embed/".$activated_global_task->tutorial_yt_url."' frameborder='0' allowfullscreen></iframe>";
	echo "</div>";
} ?>
<?php if(is_object($team_member)) { ?>
	<?php if($active_tasks) { ?>
	<div id="active_task_list">
		<table id=>
			<tr><th>Active Task List</th><th></th><th>Due Date</th><th>Actions</th></tr>
			<?php foreach($active_tasks as $active_task): ?>
			<tr>
				<td><?php $active_task->display_full_task_lesson(); ?></td>
				<td><?php echo $active_task->task_name; ?></td>
				<td><?php echo $active_task->task_due_date; ?></td>
				<td><form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
					<input type='hidden' name='task_id' value='<?php echo $active_task->id; ?>'>
					<input type='submit' name='task_deactivated' value='Deactivate'>
					</form>
					<form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
					<input type='hidden' name='task_id' value='<?php echo $active_task->id; ?>'>
					<input type='submit' name='task_completed' value='Complete'>
					</form></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<br />
	<?php } ?> <!-- End of if($active_tasks) -->
	<?php if($actionable_tasks) { ?>
	<div id="actionable_task_list">
		<table id=>
			<tr><th>Actionable Task List</th><th></th><th>Due Date</th><th>Actions</th></tr>
			<?php foreach($actionable_tasks as $actionable_task): ?>
			<?php
			echo "<tr ";
						if (strtotime($actionable_task->task_due_date) < time()) {
							echo "class='overdue'";
						}
						echo ">";
			?>
				<td><?php $actionable_task->display_full_task_lesson(); ?></td>
				<td><?php echo $actionable_task->task_name; ?></td>
				<td><?php echo $actionable_task->task_due_date; ?></td>
				<td><form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
					<input type='hidden' name='task_id' value='<?php echo $actionable_task->id; ?>'>
					<input type='submit' name='task_activated' value='Activate'>
					</form></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<br />
	<?php } ?> <!-- End of if($actionable_tasks) -->
	<?php if($actionable_assets) { ?>
	<div id="asset_list">
		<table>
			<tr><th>Actionable Asset List</th><th></th><th></th><th>Due Date</th><th>Actions</th></tr>
			<?php foreach($actionable_assets as $task):
			// Need a different layout depending on the type of asset
			// global_task_id
			// 03	Shoot				3 Minutes 
			// 27	Shoot MC			Writing
			// 28	Shoot HW			Writing
			// 53	Shoot MC			Holidays
			// 48	Make Title Slide	Holidays
			// 49	Make Thumbnail		Holidays
			// 50	Make Images			Holidays
			// 51	Record Male Audio	Listening
			// 52	Record Female Audio Listening
			// 78	Make Title GFX		Weekly Words
			// 63	Shoot with MC		Weekly Words
			// 64	Shoot English MC	Pronunciation
			// 89	Shoot Target MC		Pronunciation
			// 62	Shoot MC			Counters
			// 81	Shoot MC Target		Counters
			// 82	Record English MC	Counters
			// 83	Record Target MC	Counters
			// 84	Shoot MC			Can Do
			// 85	Record Target MC	Can Do
			// 86	Record English MC	Can Do
			// 87	Record Scene Audio	Can Do
			// 88	Make Scene Animatio	Can Do
			echo "<tr><td>";
			echo $task->display_full_task_lesson();
			echo "</td>";
			echo "<td>".$task->task_name."</td><td>";
			switch($task->global_task_id) {
				case 3:
				case 27:
				case 28:
				case 53:
				case 63:
				case 64:
				case 89:
				case 62:
				case 81:
				case 84:
					// Video Shoot
					break;
				case 51:
				case 52:
				case 82:
				case 83:
				case 85:
				case 86:
				case 87:
					// Audio Recording
					break;
				case 48:
				case 78:
					echo $task->lesson_title;
					break;
				case 49:
					// Design
					break;
				case 50:
					$images = Link::get_links_for_asset($task->id);
					$i = 1;
					if ($images) {
						echo "<ul>";
						foreach ($images as $image) {
							echo "<li><a href='".$image->url."'>".$image->text."</a></li>";
						}
						echo "</ul>";
					} else {
						echo "Images not set";
					}
					break;
			}
			echo "</td>";
			echo "<td>".$task->task_due_date."</td>";
			?>
				<td><form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
					<input type='hidden' name='task_id' value='<?php echo $task->id; ?>'>
					<input type='submit' name='asset_completed' value='Complete'>
					<input type='submit' name='asset_completed_and_delivered' value='Complete and Deliver'>
					</form></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<br />
	<?php } ?> <!-- End of if($actionable_assets) -->
	<?php if($deliverable_assets) { ?>
	<div id="deliverable_asset_list">
		<table>
			<tr><th>Deliverable Asset List</th><th></th><th>Due Date</th><th>Actions</th></tr>
			<?php foreach($deliverable_assets as $task):
			echo "<tr>";
			echo "<td>";
			echo $task->display_full_task_lesson();
			echo "</td>";
			echo "<td>".$task->task_name."</td>";
			echo "<td>".$task->task_due_date."</td>";
			?>
					<td><form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
					<input type='hidden' name='task_id' value='<?php echo $task->id; ?>'>
					<input type='submit' name='asset_delivered' value='Deliver'>
					</form></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<br />
	<?php } ?> <!-- End of if($deliverable_assets) -->
	<?php if($unresolved_issues) { ?>
	<div id="issues-list">
		<table>
			<tr><th>Lesson</th><th>Creator</th><th>Timecode</th><th>Description</th><th>Actions</th></tr>
			<?php foreach($unresolved_issues as $issue): ?>
			<?php $task_for_issue_id = Task::find_by_id($issue->task_id); ?>
			<tr>
				<td><?php echo $task_for_issue_id->display_full_task_lesson(); ?></td>
				<td><?php echo $issue->issue_creator; ?></td>
				<td><?php echo $issue->issue_timecode; ?></td>
				<td><?php echo $issue->issue_body; ?></td>
				<td><form action='task-sheet.php?member=<?php echo $team_member_id; ?>' method='post'>
					<input type='hidden' name='issue_id' value='<?php echo $issue->id; ?>'>
					<input type='submit' name='issue_completed' value='Fixed'>
					</form></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<?php } ?> <!-- End of if($unresolved_issues) -->
<?php } else { echo "Member not found";	} ?>  <!-- End of if(is_object($team_member)) -->
</div>

<?php include_layout_template('footer.php'); ?>