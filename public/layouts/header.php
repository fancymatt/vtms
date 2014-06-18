<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>ILL VTMS</title>	
		<link rel="stylesheet" href="css/foundation.min.css" />
		<script src="js/vendor/modernizr.js"></script>
	</head>
	<body>
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
				echo "<a href='user-dashboard.php'>Your Dashboard</a> | ";
				echo '<a href="logout.php">Log Out</a>';
			}
		echo "</p>";
	}
	
	?>
	</div>
	<nav class="top-bar" data-topbar>
		<ul class="title-area">
			<li class="name">
			<h1><a href="#">VTMS</a></h1>
			</li>
			<li class="toggle-topbar menu-icon"><a href="#">Menu</a></li>
		</ul>
		<section class="top-bar-section">
			<ul>
				<li><a href="lesson-db.php">Lesson DB</a></li>
				<li><a href="tasks.php">Tasks</a></li>
				<li><a href="recent-assets.php">Assets</a></li>
				<li><a href="recent-issues.php">Issues</a></li>
			</ul>
			
			<?php if($session->is_admin()) { ?>
			<ul class="left">
				<li class="has-dropdown">
					<a href="#">Management</a>
					<ul class="dropdown">
						<li><a href="recent-lessons.php">Lessons</a></li>
						<li><a href="qa.php">QA</a></li>
						<li><a href="operations.php">Operations</a></li>
						<li><a href="publishing.php">Publishing</a></li>
						<li><a href="publishing-on-date.php">Today</a></li>
					</ul>
				</li>
			</ul>
			<ul class="left">
				<li class="has-dropdown">
					<a href="#">Members</a>
					<ul class="dropdown">
					<?php 
						$members = Member::find_all_members();
						foreach($members as $member): ?>
						<li><a href="task-sheet.php?member=<?php echo $member->id; ?>">
						<?php echo $member->first_name; ?></a></li>
						<?php endforeach; ?>
						<li><a href="admin-users.php">Manage Users</a></li>
					</ul>
				</li>
			</ul>
			<ul class="left">
				<li>
					
				</li>
			</ul>
			<?php } ?>
		</section>
	</nav>