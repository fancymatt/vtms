<?php require_once("../includes/initialize.php"); ?>
<?php	
	confirm_logged_in();
	$language_series_id = $db->escape_value($_GET['id']);
	$language_series = LanguageSeries::find_by_id($language_series_id);
	$series = Series::find_by_id($language_series->series_id);
	$language = Language::find_by_id($language_series->language_id);
	$language_series_lessons = Lesson::find_all_lessons_for_language_series($language_series_id);

	//$ill_tv_code = $language_series->generate_ill_tv_code();
?>
<?php $page_title = ucwords($language->code)." ".ucwords($series->code); ?>

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
	
	<div id="tabs" class="row">
		<ul class="tabs" data-tab>
			<li class="tab-title active"><a href="#panel-lessons">Lessons</a></li>
			<li class="tab-title"><a href="#panel-illtv">ILL TV</a></li>
		</ul>
	</div>
	
	<div class="tabs-content">
		<div class="content active" id="panel-lessons">
			<div id="task-list-table" class="row">
				<div class="small-12 columns">
				  <ol class="group">
						<?php foreach($language_series_lessons as $lesson): ?> <!-- For every lesson -->
						<div class="group-item<?php echo $lesson->files_moved ? " ready" : ""?>">
      				<div class="lesson-info">
        				<a href="lesson.php?id=<?php echo $lesson->id; ?>">
        				<p class="lesson-title"><?php echo $lesson->number.". ".$lesson->title; ?></p>
        				<p class="date"><?php echo "Publish Date: ".$lesson->publish_date; ?></p>
        				</a>
      				</div>
      				<div class="lesson-status">
      				  <p class="lesson-status-item">
      				    <img src="<?php echo $lesson->is_shot ? "img/lesson-status-yes-shot.png" : "img/lesson-status-not-shot.png"?>">
      				  </p>
      				  <p class="lesson-status-item">
      				    <img src="<?php echo $lesson->is_checkable ? "img/lesson-status-yes-checkable.png" : "img/lesson-status-not-checkable.png"?>">
      				  </p>
      				  <p class="lesson-status-item">
      				    <img src="<?php echo $lesson->checked_language ? "img/lesson-status-yes-language.png" : "img/lesson-status-not-language.png"?>">
      				  </p>
      				  <p class="lesson-status-item">
      				    <img src="<?php echo $lesson->checked_video ? "img/lesson-status-yes-video.png" : "img/lesson-status-not-video.png"?>">
      				  </p>
      				  <p class="lesson-status-item">
      				    <img src="<?php echo $lesson->files_moved ? "img/lesson-status-yes-moved.png" : "img/lesson-status-not-moved.png"?>">
      				  </p>
      				</div>
        		</div>
        	<?php endforeach; ?> <!-- End for every series -->
							
							<tr> <!-- Add new list item row -->
								<td colspan="6"><a href="new-language-series.php?inSeries=<?php echo $series->id; ?>">Add new Language Series</a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="content" id="panel-illtv">
			<div id="section-header" class="row">
				<header class="small-12 columns"><h4>ILL TV</h4></header>
			</div>
			<div id="ill-tv-code" class="row">
				<textarea><?php echo $ill_tv_code; ?></textarea>
			</div>
		</div>
	</div>
<?php include_layout_template('footer.php'); ?>