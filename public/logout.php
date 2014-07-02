<?php require_once("../includes/initialize.php"); ?>
<?php
	// v1: simple logout
	// session_start();
	$session->logout();
	redirect_to("login.php");
?>