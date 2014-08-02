<?php require_once("../includes/initialize.php"); ?>
<?php	
	confirm_logged_in();
	$series = Series::find_by_id($db->escape_value($_GET['id']));
	$languageSeries = LanguageSeries::find_all_language_series_for_series($db->escape_value($_GET['id']));
?>

<?php $page_title = ucwords($series->code); ?>

<?php include_layout_template('header.php'); ?>
	
	<div id="breadcrumbs" class="row">
		<ul class="breadcrumbs">
			<li><a href="lesson-db.php">Lesson DB</a></li>
			<li class="current"><a href="#"><?php echo $series->title; ?></a></li> 
		</ul>
	</div>
	
	<div id="page-header" class="row">
		<header class="small-11 small-centered columns">
			<h3><?php echo $series->title; ?></h3>
		</header>
	</div>
	
	<div id="series-list-table" class="row">
		<div class="small-11 small-centered columns">
		  <ol class="group">
    		<?php foreach($languageSeries as $row): ?> <!-- For every series -->
    		<div class="group-item">
    		  <div class="series-status">
    		    <p>Total Running Time: <span class="strong"><?php echo $row->total_trt; ?></span></p>
    		  </div>
    	    <div class="series-info">
    				<a class="series-title" href="language-series.php?id=<?php echo $row->id; ?>"><?php echo $row->language_series_title." (".$row->level_code.")"; ?></a>
    			</div>
    		</div>
    		<?php endforeach; ?> <!-- End for every series -->
    		<div class="add">
    		  <a href="new-series.php">Add new Series</a>
    		</div>
		  </ol>
		</div>
  </div>
	
<?php include_layout_template('footer.php'); ?>