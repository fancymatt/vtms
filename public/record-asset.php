<?php require_once("../includes/initialize.php"); ?>
<?php
	$asset_id = $db->escape_value($_GET['id']);
	$asset = Task::find_by_id($asset_id);
	$lesson = Lesson::find_by_id($asset->lesson_id);
	
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
		<tr><th>Shot</th><th>Script</th><th>Script English</th><th>Actions</th></tr>
		<?php foreach($shots as $shot): ?>
					<tr>
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
							Actions
						</td>
					</tr>
				<?php endforeach; ?>
				</table>
	<p><a href="lesson-db.php"><- Return to Parent List</a></p>
</div>

<?php include_layout_template('footer.php'); ?>