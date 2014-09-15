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

	<div class="small-12 medium-8 medium-centered columns">
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
	</div>
	
	<div class="small-12 medium-8 medium-centered columns">
			<h4><?php echo $language_series->language_series_title." (".ucwords($language_series->level_code).")"; ?></h4>
	</div>
	
	<div class="small-12 medium-8 medium-centered columns">
  	<div class="task-sheet-tabs">
  		<ul class="tabs" data-tab>
  			<li class="tab-title active"><a href="#panel-lessons">Lessons</a></li>
  			<li class="tab-title"><a href="#panel-illtv">ILL TV</a></li>
  		</ul>
  	</div>
		
  	<div class="tabs-content">
  		<div class="content active" id="panel-lessons">
  			<div id="task-list-table" class="row">
				  <ol class="group">
						<?php foreach($language_series_lessons as $lesson): ?> <!-- For every lesson -->
						<div class="group-item<?php echo $lesson->files_moved ? " ready" : ""?>">
              <?php $lesson->display_lesson_status_bar(); ?>
              <div class="group-item-body">
    				    <div class="group-item-content">
      				    <div class="lesson-info">
            				<a class="lesson-title" href="lesson.php?id=<?php echo $lesson->id; ?>"><?php echo $lesson->number.". ".$lesson->title; ?></a>
                  </div>
                  <div class="group-item-metadata">
                    <p><?php echo "Publish on ".$lesson->publish_date; ?></p>
                  </div>
    				    </div>
              </div>
        		</div>
        	<?php endforeach; ?> <!-- End for every series -->
						<div class="add">
						  <a href="new-lesson.php?inLanguageSeries=<?php echo $language_series_id; ?>">Add new Lesson</a>
						</div>
  				</ol>
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
	</div>
<?php include_layout_template('footer.php'); ?>