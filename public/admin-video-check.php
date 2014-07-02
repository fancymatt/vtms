<?php require_once("../includes/initialize.php"); ?>
<?php
	if (!$session->is_admin()) {
		$_SESSION['message'] = "You need admin privileges to access this page.";
		redirect_to('login.php');
	}
	if($_POST['mark_lesson_as_checked']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->checked_video = 1;
		$lesson->update();
	}
	
	if($_POST['move_lesson']) {
		$qa_lesson_id = $db->escape_value($_POST['qa_lesson_id']);
		$lesson = Lesson::find_by_id($qa_lesson_id);
		$lesson->files_moved = 1;
		$lesson->update();
	}
	
	$moveable_lessons = Lesson::find_all_moveable_lessons();
	$sort_by = $db->escape_value($_GET['sort']);
	$language_checked_lessons = Lesson::find_all_ready_to_video_check_lessons($sort_by);
?>

<?php include_layout_template('header.php'); ?>
	
	<div id="breadcrumbs" class="row">
		<ul class="breadcrumbs">
			<li><a href="operations.php">Operations</a></li>
			<li class="current"><a href="admin-video-check.php">Video Checking</a></li>
		</ul>
	</div>
	
	<?php if($message) { ?>
	<div data-alert class="alert-box">
	  <?php echo $message; ?>
	  <a href="#" class="close">&times;</a>
	</div>
	<?php } ?>
	
	<div id="videos-to-move" class="row">
		<div class="small-12 columns">
			<h4>Move These</h4>
			<table>
				<tr>
					<th>Lesson</th>
					<th>Actions</th>
					<th>Due Date</th>
				</tr>
				<?php 
				if(!$moveable_lessons) {
					echo "<td>No lessons</td>";
				} else {
					foreach($moveable_lessons as $qa_lesson) {
						echo "<tr>";
						echo "<td>";
						echo $qa_lesson->display_full_lesson();
						echo "</td>";
						echo "<td><form action='admin-video-check.php' method='post'>";
						echo "<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'>"; ?>
						<input type="image" src="img/icon-move-files.png" name='move_files' value='Move Files' data-tooltip class="has-tip" title="Move Files">
						<?php
						echo "</td>";
						echo "<td>{$qa_lesson->publish_date}</td>";
						echo "</tr>";
					} 
				 } ?>		
			</table>
		</div>
		
		<ol class="commit-group">
          <li class="commit commit-group-item js-navigation-item js-details-container">
            <img alt="Matt Henry" class="gravatar js-avatar" data-user="7379405" height="36" src="https://avatars0.githubusercontent.com/u/7379405?s=140" width="36" />
            <p class="commit-title  js-pjax-commit-title">
              <a href="/henrymatt/vtms/commit/055997cf7cffdfc60ca3e3fd53e4d0a68c3b516b" class="message" data-pjax="true" title="Removes scripts folder from git and updates .gitignore">Removes scripts folder from git and updates .gitignore</a>
            
            </p>
            <div class="commit-meta">

              <div class="commit-links">
                <button aria-label="Copy SHA" class="js-zeroclipboard minibutton zeroclipboard-button" data-clipboard-text="055997cf7cffdfc60ca3e3fd53e4d0a68c3b516b" data-copied-hint="Copied!" type="button"><span class="octicon octicon-clippy"></span></button>

                <a href="/henrymatt/vtms/commit/055997cf7cffdfc60ca3e3fd53e4d0a68c3b516b" class="gobutton ">
                  <span class="sha">055997cf7c<span class="octicon octicon-arrow-small-right"></span></span>
                </a>

                <a href="/henrymatt/vtms/tree/055997cf7cffdfc60ca3e3fd53e4d0a68c3b516b" class="browse-button" title="Browse the code at this point in the history" rel="nofollow">Browse code <span class="octicon octicon-arrow-right"></span></a>
              </div>

              <div class="authorship">
                <span class="author-name"><a href="/henrymatt" rel="author">henrymatt</a></span>
                authored <time datetime="2014-06-16T16:44:23+09:00" is="relative-time">June 16, 2014</time>

              </div>
            </div>
          </li>
	
		<div class="small-12 columns">
			<h4>Check These</h4>
				<ol class="lesson-group">
				<?php
				if(!$language_checked_lessons) {
					echo "<tr><td>No lessons</td><tr>";
				} else {
					foreach($language_checked_lessons as $qa_lesson): ?>
					<li class="lesson-group-item">
						<p class="lesson-title"><?php echo $qa_lesson->display_full_lesson(); ?></p>
						<div class="lesson-meta">
							<p class="lesson-due-date">
								<?php echo $qa_lesson->publish_date; ?>
							</p>
						</div>
						<ul class="lesson-actions">
							<li class="lesson-action-item">
								<a class="item" href="issues-for-lesson.php?id=<?php echo $qa_lesson->id; ?>"><img src="img/icon-add-issue.png"></a>
							</li>
							<li>
								<form action='admin-video-check.php' method='post'>
									<input type='hidden' name='qa_lesson_id' value='{$qa_lesson->id}'>
									<input class="item" type='image' src="img/icon-move-files.png" name='mark_lesson_as_checked' value='Mark as Checked' data-tooltip class="has-tip" title="Mark Lesson as Checked">
								</form>
							</li>
						</ul>						
					</div>
					<?php endforeach; ?>
				<?php } ?>
			</table>
		</div>
	</div>
		
<?php include_layout_template('footer.php'); ?>