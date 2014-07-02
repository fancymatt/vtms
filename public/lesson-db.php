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
		<table>
			<thead>
				<tr>
					<th width="600">Name</th>
					<th width="150">TRT</th>
					<?php if ($session->is_admin()) { ?> <!-- If logged in, show actions column -->
					<th width="150">Actions</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($series as $row): ?> <!-- For every series -->
				<tr>
					<td><a href="series.php?id=<?php echo $row->id; ?>"><?php echo $row->title; ?></a></td>
					<td><?php echo $row->series_trt; ?></td>
					
					<?php if ($session->is_admin()) { ?>
					<td>
						<a href="edit-series.php?id=<?php echo $row->id; ?>">Edit</a>
					</td>
					<?php } ?>
						
				</tr>
				<?php endforeach; ?> <!-- End for every series -->
				
				<tr> <!-- Add new list item row -->
					<td><a href="new-series.php">Add new Series</a></td>
					<td></td>
					<?php if ($session->is_admin()) { ?> <!-- If you're not logged in, no actions column -->
					<td></td>
					<?php } ?>
			</tbody>
		</table>
		</div>
	</div>

<?php include_layout_template('footer.php'); ?>