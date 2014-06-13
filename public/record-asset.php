<?php require_once("../includes/initialize.php"); ?>
<?php
	$asset_id = $db->escape_value($_GET['id']);
	$asset = Task::find_by_id($asset_id);
	$lesson = Lesson::find_by_id($asset->lesson_id);
	
	if($_POST['shot_completed']) {
		$shot_id = $_POST['shot_id'];
		$shoot_notes = $db->escape_value($_POST['shoot_notes']);
		$completed_shot = Shot::find_by_id($shot_id);
		$completed_shot->is_completed = 1;
		$completed_shot->update();
	}
	
	if($_POST['shot_uncompleted']) {
		$shot_id = $_POST['shot_id'];
		$uncompleted_shot = Shot::find_by_id($shot_id);
		$uncompleted_shot->is_completed = 0;
		$uncompleted_shot->update();
	}
	
	
	$shots = Shot::find_all_shots_for_asset($asset_id)
?>
<?php include_layout_template('header.php'); ?>

	<div id="parent-info">
		<h2><?php echo $parent->name; ?></h2>
		<p><?php echo $parent->attribute; ?></p>
	</div>

	<div id="lesson-header">
		<h3><?php $lesson->display_full_lesson_navigation(); ?></h3>
		<p><a href='lesson-script.php?id=<?php echo $lesson->id; ?>'>Return to Script</a>
	</div>
	
	<div id="list">
	<table>
		<tr><th>Shot</th><th>Script</th><th>Script English</th><th>Recording Comments</th><th>Actions</th></tr>
		<?php foreach($shots as $shot): ?>
					<tr<?php if($shot->is_completed) { echo " class='completed'"; } ?>> 
						<td>
							<?php echo "{$shot->section} {$shot->shot} - {$shot->type}"; ?>
						</td>
						<td>
							<?php echo $shot->script; ?>
						</td>
						<td>
							<?php echo $shot->script_english; ?>
						</td>
						<td>
							<form method="post" action="record-asset.php?id=<?php echo $asset_id; ?>">
							<input type="hidden" name="shot_id" value="<?php echo $shot->id; ?>">
							<textarea name="shoot_notes" rows=5 cols=20><?php echo $shot->script_video; ?></textarea>
							<input type="submit" name="shot_completed" value="Mark as Complete"></form>
						</td>
						<td>
							<?php if($shot->is_completed) { ?>
							<form method="post" action="record-asset.php?id=<?php echo $asset_id; ?>">
							<input type="hidden" name="shot_id" value="<?php echo $shot->id; ?>">
							<input type="submit" name="shot_uncompleted" value="Mark as Incomplete"></form>	
							<?php } ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</table>
	<p><a href="lesson-db.php"><- Return to Parent List</a></p>
</div>

<?php include_layout_template('footer.php'); ?>