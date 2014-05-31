<?php require_once("../includes/initialize.php"); ?>
<?php
	$session->confirm_logged_in();
	
	if($_POST['submit_new_series']) {
		$new_series_name = $database->escape_value($_POST['series_name']);
		$new_series_code = $database->escape_value($_POST['series_code']);
		
		$required_fields = array("series_name", "series_code");
		validate_presences($required_fields);
		
		if(!empty($errors)) {
			$_SESSION["errors"] = $errors;
			redirect_to("new-series.php");
		}
		$sql  = "INSERT INTO series (title, code) ";
		$sql .= "VALUES ('{$new_series_name}', '{$new_series_code}') ";
		$result = $database->query($sql);
		$new_series_id = mysql_insert_id();
		redirect_to("series.php?series={$new_series_id}");
	}
?>

<?php include_layout_template('header.php'); ?>
	<div>
		<?php $errors = $session->errors(); ?>
		<p><span id="errorMessage"></span></p>
		<?php echo $session->form_errors($errors); ?>
		<h2>Add New Series</h2>
		<form action="new-series.php" method="POST" id="new_series_form">
			<p><label for="series_name">Name:</label> <input type="text" size="50" name="series_name" id="series_name"></p>
			<p><label for="series_code">Code:</label> <input type="text" name="series_code"></p>
			<p><input type="submit" name="submit_new_series" id="submit_new_series"></p>
		</form>
	</div>
	<script src="script.js"></script>
<?php include_layout_template('footer.php'); ?>