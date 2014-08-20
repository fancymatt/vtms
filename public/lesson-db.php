<?php require_once("../includes/initialize.php"); ?>
<?php $series = Series::find_all_limit(0); ?>
<?php $page_title = "Lesson DB"; ?>

<?php include_layout_template('header.php'); ?>
  <div class="small-12 medium-8 medium-centered columns">
    <div id="breadcrumbs" class="row">
    	<ul class="breadcrumbs">
    		<li class="current"><a href="lesson-db.php">Lesson DB</a></li>
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
		<h4>Video Series List</h4>
	</div>
	
	<div class="small-12 medium-8 medium-centered columns">
	  <ol class="group">
			<?php foreach($series as $row): ?> <!-- For every series -->
			<div class="group-item">
			  <div class="series-status">
			    <p>Total Running Time: <span class="strong"><?php echo $row->series_trt; ?></span></p>
			  </div>
		    <div class="series-info">
  				<a class="series-title" href="series.php?id=<?php echo $row->id; ?>"><?php echo $row->title; ?></a>
				</div>
  		</div>
			<?php endforeach; ?> <!-- End for every series -->
			<div class="add">
			  <a href="new-series.php">Add new Series</a>
			</div>
	  </ol>
	</div>

<?php include_layout_template('footer.php'); ?>