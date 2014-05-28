<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$days_past = 1;
	$recent_assets = Task::get_recently_delivered_assets($days_past);
	$actionable_assets = Task::get_all_actionable_asset_tasks();
	$deliverable_assets = Task::get_all_deliverable_asset_tasks();
	$logged_in_user = User::find_by_id($session->user_id);
?>

<?php include_layout_template('header.php'); ?>
		
		<div id="recent_tasks">
		<h2 id="main_title">Assets Completed in the last <?php if ($days_past == 1) { echo "day"; } else { echo $days_past . " days"; } ?></h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Member</th><th>Lesson</th><th>Task</th><th>Completed Time</th></tr>
			<?php 
				if ($recent_assets) {
					foreach($recent_assets as $task) {
					echo "<tr>";
					echo "<td>";
					if($session->is_admin()) {
						echo "<a href='taskSheet.php?member={$task->team_member_id}'>{$task->team_member_name}</a>";
					} else {
						echo $task->team_member_name;
					}
					echo "</td>";
					echo "<td>";
					echo $task->display_full_task_lesson();
					echo "</td>";
					echo "<td>{$task->task_name}</td>";
					echo "<td>" . $logged_in_user->local_time($task->completed_time) . "</td>";
					echo "</tr>";
					}
				} else {
					echo "<tr><td>No assets have been delivered in the specified period.</td><td></td><td></td><td></td><tr>";
				} ?>
		</table>
		</div>	
		
		<div id="ready_to_deliver_assets">
		<h2 id="main_title">Deliverable Assets</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Member</th><th>Lesson</th><th>Task</th><th>Due Date</th></tr>
			<?php if ($deliverable_assets) {
				foreach($deliverable_assets as $task) {
				echo "<tr>";
				echo "<td>";
					if($session->is_admin()) {
						echo "<a href='taskSheet.php?member={$task->team_member_id}'>{$task->team_member_name}</a>";
					} else {
						echo $task->team_member_name;
					}
					echo "</td>";
				echo "<td>";
				echo $task->display_full_task_lesson();
				echo "</td>";
				echo "<td>{$task->task_name}</td>";
				echo "<td>" . $task->task_due_date . "</td>";
				echo "</tr>";
				}
			} else {
				echo "<tr><td>No deliverable assets at the moment.</td><td></td><td></td><td></td><tr>";
			} ?>
		</table>
		</div>
		
		<div id="actionable_assets">
		<h2 id="main_title">Actionable Assets</h2>
		<?php if ($message) {
			echo "<p>{$message}</p>";
		} ?>
		<table>
			<tr><th>Member</th><th>Lesson</th><th>Task</th><th>Due Date</th></tr>
			<?php if ($actionable_assets) {
				foreach($actionable_assets as $task) {
				echo "<tr>";
				echo "<td>";
					if($session->is_admin()) {
						echo "<a href='taskSheet.php?member={$task->team_member_id}'>{$task->team_member_name}</a>";
					} else {
						echo $task->team_member_name;
					}
					echo "</td>";
				echo "<td>";
				echo $task->display_full_task_lesson();
				echo "</td>";
				echo "<td>";
				echo $task->task_name;
				if($task->global_task_id == 50 && $session->is_admin()) {
					$images = Link::get_links_for_asset($task->id);
					if($images) {
						echo " (<a href='editImagesForAsset.php?id=".$task->id."'>Edit Images</a>)";
					} else {
						echo " (<strong><a href='editImagesForAsset.php?id=".$task->id."'>Add Images</a></strong>)";
					}
					
				}
				echo "</td>";
				echo "<td>" . $task->task_due_date . "</td>";
				echo "</tr>";
				}
			} else {
				echo "<tr><td>No actionable assets at the moment.</td><td></td><td></td><td></td><tr>";
			} ?>
		</table>
		</div>		
<?php include_layout_template('footer.php'); ?>