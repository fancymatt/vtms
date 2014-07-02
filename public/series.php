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
		<header class="medium-10 medium-margin-1 columns">
			<h3><?php echo $series->title; ?></h3>
		</header>
	</div>
	
	<div id="series-list-table" class="row">
		<div class="medium-11 medium-margin-1 small-12 columns">
			<table>
				<thead>
					<tr>
						<th width="600">Name</th>
						<th width="150">Series TRT</th>
						<?php if ($session->is_admin()) { ?> <!-- If logged in, show actions column -->
						<th width="150">Actions</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach($languageSeries as $series): ?> <!-- For every language series -->
					<tr>
						<td><a href="language-series.php?id=<?php echo $series->id; ?>"><?php $series->display_full_language_series(); ?></a></td>
						<td><?php echo $series->total_trt; ?></td>
						
						<?php if ($session->is_admin()) { ?>
						<td>
							<a href="edit-language-series.php?id=<?php echo $row->id; ?>">Edit</a>
						</td>
						<?php } ?>
							
					</tr>
					<?php endforeach; ?> <!-- End for every series -->
					
					<tr> <!-- Add new list item row -->
						<td colspan="3"><a href="new-language-series.php?inSeries=<?php echo $series->id; ?>">Add new Language Series</a></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
<?php include_layout_template('footer.php'); ?>