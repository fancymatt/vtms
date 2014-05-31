<?php require_once("../includes/initialize.php"); ?>
<?php
	$parent_id = $db->escape_value($_GET['id']);
	$records = ChildObject::find_all_child_for_parent($parent_id, $child_table_name, $parent_table_name, $group_by_sql);
	$parent = ParentObject::find_by_id($parent_id);
?>

<?php include_layout_template('header.php'); ?>
<div id="parent-info">
	<h2><?php echo $parent->name; ?></h2>
	<p><?php echo $parent->attribute; ?></p>
</div>
<div id="list">
	<p><a href="lesson-db.php"><- Return to Parent List</a></p>
	<table>
		<tr><th>Title</th><th>Attribute</th><th>Actions</th></tr>
		<?php 
		foreach($records as $record) {
			echo "<tr>";
			echo "<td>{$record->name}</td>";
			echo "<td>{$record->attribute}</td>";
			echo "<td><a href='viewRecord.php?id={$record->id}'>View</a>";
			if ($session->is_admin()) {
				echo " | <a href='editRecord.php?id={$record->id}'>Edit</a>";
			}
			echo "</td></tr>";
		} ?>
	</table>
	<p><a href="lesson-db.php"><- Return to Parent List</a></p>
</div>
<?php include_layout_template('footer.php'); ?>