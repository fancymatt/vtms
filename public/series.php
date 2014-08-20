<?php require_once("../includes/initialize.php"); ?>
<?php	
	confirm_logged_in();
	$series = Series::find_by_id($db->escape_value($_GET['id']));
	$languageSeries = LanguageSeries::find_all_language_series_for_series($db->escape_value($_GET['id']));
?>

<?php $page_title = ucwords($series->code); ?>

<?php include_layout_template('header.php'); ?>
	<div class="small-12 medium-8 medium-centered columns">
  	<div id="breadcrumbs" class="row">
  		<ul class="breadcrumbs">
  			<li><a href="lesson-db.php">Lesson DB</a></li>
  			<li class="current"><a href="#"><?php echo $series->title; ?></a></li> 
  		</ul>
  	</div>
	</div>
	
	<?php if($message) { ?>
  <div data-alert class="alert-box">
    <?php echo $message; ?>
    <a href="#" class="close">&times;</a>
  </div>
  <?php } ?>
	
	<div class="small-12 medium-8 medium-centered columns">
		<h4><?php echo $series->title; ?></h4>
	</div>
	
	<div class="small-12 medium-8 medium-centered columns">
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
  		  <a href="new-language-series.php?inSeries=<?php echo $series->id; ?>">Add new Language in this Series</a>
  		</div>
	  </ol>
	</div>
	
<?php include_layout_template('footer.php'); ?>