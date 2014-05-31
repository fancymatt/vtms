<?php
	require_once("includes/session.php");
	require_once("includes/connection.php");
	require_once("includes/functions.php");
	
	confirm_logged_in();
	
	$user = find_user_by_id($_GET['user']);
	if (!$user) {
		redirect_to("admin-manage-users.php");
	}
	
	$user_id = $user["pkUser"];
	$query = "DELETE FROM user WHERE pkUser = {$user_id} LIMIT 1";
	$result = mysql_query($query);
	
	if ($result && mysql_affected_rows() == 1) {
		$_SESSION["message"] = "User deleted.";
		redirect_to("admin-manage-users.php");
	} else {
		$_SESSION["message"] = "User deletion failed.";
		redirect_to("admin-manage-users.php");
	}
?>