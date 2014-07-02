<?php require_once("../includes/initialize.php"); ?>
<?php
	$session->confirm_logged_in();
	
	if($_POST['edited_record']) { // Record has been edited
		$edited_record_id = $database->escape_value($_POST['edited_record_id']);
		$edited_record = Object::find_by_id($edited_record_id);
		$edited_record->instance_variable = $edited_record_instance_variable;
		$edited_record->update();
		redirect_to("editRecord.php?record={$edited_series_id}");	
		if(!empty($session->errors)) {
			$_SESSION["errors"] = $errors;
		}
	} else if($_POST['deleted_record']) { // Record has been deleted
		$deleted_record_id = $database->escape_value($_POST['deleted_record_id']);
		$deleted_record = Object::find_by_id($deleted_record_id);
		$deleted_record->delete();
		redirect_to("lesson-db.php");
	} else { // Page is displaying for first time
		$current_record_id = $_GET['id'];
		$current_record = Object::find_by_id($current_record_id);
		if (!$current_record->name) {
			redirect_to("lesson-db.php");
		}	
	}
?>

<?php include_layout_template('header.php'); ?>
	<div>
		<?php $errors = $session->errors(); ?>
		<?php echo $session->form_errors($errors); ?>
		<h2>Edit Record: <?php echo $current_record->name; ?></h2>
		<form action="editRecord.php?id=<?php echo $current_record_id; ?>" method="POST">
			<p><label for="editable_field">Field:</label> <input type="text" size="50" name="editable_field" value="<?php echo $current_record->instance_variable; ?>"></p>
			<input type="hidden" name="edited_record_id" value="<?php echo $current_record->id; ?>">
			<p><input type="submit" name="edited_record" id="edited_record"></p>
		</form>
	</div>
	<form action="editRecord.php?id=<?php echo $current_record_id; ?>" method="POST">
		<input type="hidden" name="deleted_record_id" value="<?php echo $current_record->id; ?>">
		<input type="submit" name="deleted_record" id="deleted_record" value="Delete Record" onclick="return confirm('Are you sure you want to delete this?')">
	</form>
<?php include_layout_template('footer.php'); ?>