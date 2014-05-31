<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>ILL VTMS</title>	
	<link rel="stylesheet" href="styles/main.css">
	</head>
	<body>
	<div id="content">
	<div id="user-info">
	<?php 
	global $session;
	
	if (!$session->is_logged_in()) {
		echo "<p>You are not logged in | <a href='login.php'>Log In</a></p>";
	} else {
		$logged_in_user = User::find_by_id($session->user_id);
		echo "<p>You're logged in as {$logged_in_user->user_name}";
			if ($logged_in_user->id > 0) {
				echo " | <a href='task-sheet.php?member=";
				echo $logged_in_user->team_member_id;
				echo "'>Your Task Sheet</a> | ";
				echo '<a href="logout.php">Log Out</a>';
			}
		echo "</p>";
	}
	
	?>
	</div>
	<div id="navbar">
	<p><h1>Video Team Management System</h1></p>
	<?php //echo "<p>".print_r($_SESSION)."</p>"; ?>
	<p><h4>
		Lesson DB: 
		<a href="lesson-db.php">Series List</a> | 
		<a href="tasks.php">Tasks</a> | 
		<a href="recent-assets.php">Assets</a> | 
		<a href="recent-issues.php">Issues</a>
		<br />
		<?php if($session->is_admin()) {
			?>
		Management: 
		<a href="recent-lessons.php">Lessons</a> |
		<a href="qa.php">QA</a> |
		<a href="operations.php">Operations</a><br />
		<?php } ?>
		
		
	<?php if ($session->is_admin()) {
		echo "Task Sheets: ";
		$members = Member::find_all_members();
		foreach($members as $member) {
			echo "<a href='task-sheet.php?member={$member->id}'>{$member->first_name}</a> | ";
		}
		echo "<br />";
		echo "Admin: ";
		echo "<a href='admin-manage-users.php'>Manage Users</a> ";
	} ?>
	</h4></p>
	</div>