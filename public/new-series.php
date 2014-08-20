<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}

	if($_POST['submit_new_series']) {
		$required_fields = array("series_name", "series_code");
		validate_presences($required_fields);
		
		if(!empty($errors)) {
			$_SESSION["message"] = "";
			foreach($errors as $key=>$value) {
  			$_SESSION["message"] .= "{$value}<br />";
  			$_SESSION["series_name"] = $database->escape_value($_POST['series_name']);
  			$_SESSION["series_code"] = $database->escape_value($_POST['series_code']);
			}
			redirect_to("new-series.php");
		} else {
  		$new_series = new Series();
  		$new_series->title = $database->escape_value($_POST['series_name']);
  		$new_series->code = $database->escape_value($_POST['series_code']);
      $new_series->create();
  		$new_series_id = mysql_insert_id();
  		$_SESSION["message"] = "{$database->escape_value($_POST['series_name'])} has been created.";
      redirect_to("series.php?id={$new_series_id}");
		}
  }
?>

<?php include_layout_template('header.php'); ?>
	<div class="small-12 medium-8 medium-centered columns">
	
	<?php if($message) { ?>
  <div data-alert class="alert-box">
    <?php echo $message; ?>
    <a href="#" class="close">&times;</a>
  </div>
  <?php } ?>
	
	  <h4>Create new series</h4>
	  
		<p><span id="errorMessage"></span></p>
		<?php echo $session->form_errors($errors); ?>
		<form action="new-series.php" method="POST" id="new_series_form">
		
			<p><span data-tooltip class="has-tip" title="This is the name that will appear in task sheets. It doesn't have to be the complete name as directed by Marketing">Name</span> 
			<input type="text" size="50" name="series_name" id="series_name" value="<?php echo $_SESSION["series_name"]; ?>"></p>
			
			<p><span data-tooltip class="has-tip" title="This is the shorthand to distinguish the series in file names.">Code</span> 
			<input type="text" name="series_code" value="<?php echo $_SESSION["series_code"]; ?>"></p>
			
			<p><input type="submit" name="submit_new_series" class="action button" id="submit_new_series"></p>
		</form>
	</div>
	<script src="script.js"></script>
<?php include_layout_template('footer.php'); ?>