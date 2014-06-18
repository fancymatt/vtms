<?php require_once("../includes/initialize.php"); ?>
<?php	
	confirm_logged_in();
	$language_series_id = $db->escape_value($_GET['id']);
	$language_series = LanguageSeries::find_by_id($language_series_id);
	$series = Series::find_by_id($language_series->series_id);
	$language_series_lessons = Lesson::find_all_lessons_for_language_series($language_series_id);
?>

<?php include_layout_template('header.php'); ?>
	
	<div id="breadcrumbs" class="row">
		<ul class="breadcrumbs">
			<li><a href="lesson-db.php">Lesson DB</a></li>
			<li><a href="series.php?id=<?php echo $series->id; ?>"><?php echo $series->title; ?></a></li>
			<li class="current">
				<a href="#">
					<?php echo $language_series->language_series_title." (".$language_series->level_code.")"; ?>
				</a>
			</li> 
		</ul>
	</div>
	
	<div id="page-header" class="row">
		<header class="medium-10 medium-margin-1 columns">
			<h3><?php echo $language_series->language_series_title." (".ucwords($language_series->level_code).")"; ?></h3>
		</header>
	</div>
	
	<div id="lesson-list-table" class="row">
		<div class="medium-11 medium-margin-1 small-12 columns">
			<table>
				<thead>
					<tr>
						<th width="50"></th>
						<th width="600">Name</th>
						<th width="150">Status</th>
						<th width="150">TRT</th>
						<th width="150">Publish Date</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($language_series_lessons as $lesson): ?> <!-- For every lesson -->
					<tr>
						<td><?php echo $lesson->number; ?></td>
						<td><a href="lesson.php?id=<?php echo $lesson->id; ?>"><?php echo $lesson->title; ?></a></td>
						<td>
							<?php
							echo "<img src='";
							echo $lesson->is_shot ? "images/is_shot.png" : "images/not_shot.png";
							echo "'>";
							echo "<img src='";
							echo $lesson->is_checkable ? "images/is_checkable.png" : "images/not_checkable.png";
							echo "'>";
							echo "<img src='";
							echo $lesson->checked_language ? "images/is_language_checked.png" : "images/not_language_checked.png";
							echo "'>";	
							echo "<img src='";
							echo $lesson->checked_video ? "images/is_video_checked.png" : "images/not_video_checked.png";
							echo "'>";
							echo "<img src='";
							echo $lesson->files_moved ? "images/is_moved.png" : "images/not_moved.png";
							echo "'>";
							?>
						</td>
						<td><?php echo seconds_to_timecode($lesson->trt); ?></td>
						<td><?php echo $lesson->publish_date; ?></td>
					</tr>
					<?php endforeach; ?> <!-- End for every series -->
					
					<tr> <!-- Add new list item row -->
						<td colspan="6"><a href="new-language-series.php?inSeries=<?php echo $series->id; ?>">Add new Language Series</a></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
		

<?php include_layout_template('footer.php'); ?>