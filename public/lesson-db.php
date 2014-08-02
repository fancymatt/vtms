<?php require_once("../includes/initialize.php"); ?>
<?php $series = Series::find_all_limit(0); ?>
<?php $page_title = "Lesson DB"; ?>

<?php include_layout_template('header.php'); ?>
	<div id="breadcrumbs" class="row">
		<ul class="breadcrumbs">
			<li class="current"><a href="lesson-db.php">Lesson DB</a></li>
		</ul>
	</div>

	<div id="page-header" class="row">
		<header class="small-11 small-centered columns">
			<h3>Video Series List</h3>
		</header>
	</div>
	
	<div id="series-list-table" class="row">
		<div class="small-11 small-centered columns">
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
	</div>

<?php include_layout_template('footer.php'); ?>