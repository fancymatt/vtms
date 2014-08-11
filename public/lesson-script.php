<?php require_once("../includes/initialize.php"); ?>
<?php		
	$lesson_id = $db->escape_value($_GET['id']);
	$lesson = Lesson::find_by_id($lesson_id);
	$language_series = LanguageSeries::find_by_id($lesson->language_series_id);
	$series = Series::find_by_id($language_series->series_id);
	
	if($_POST['edited_script']) {
		$shot_ids = $_POST['shot_id'];
		$asset_ids = $_POST['asset'];
		$sections = $_POST['section'];
		$scripts = $_POST['script'];
		$types = $_POST['type'];
		$shots = $_POST['shot'];
		$scripts_english = $_POST['script_english'];
		$i = 0;
		if($shot_ids) {
			foreach($shot_ids as $shot_id) {
				$shot = Shot::find_by_id($shot_id);
				$shot->asset_id = $asset_ids[$i];
				$shot->section = $sections[$i];
				$shot->shot = $shots[$i];
				$shot->type = $types[$i];
				$shot->script = $scripts[$i];
				$shot->script_english = $scripts_english[$i];
				$shot->update();
				$i++;
			}
		}
		
		$new_shot_ids = $_POST['new_ID'];
		$new_asset_ids = $_POST['new_asset_id'];
		$new_sections = $_POST['new_section'];
		$new_scripts = $_POST['new_script'];
		$new_shots = $_POST['new_shot'];
		$new_types = $_POST['new_type'];
		$new_scripts_english = $_POST['new_script_english'];
		$i = 0;
		if($new_shot_ids) {
			foreach($new_shot_ids as $new_shot_id) {
				$shot = new Shot();
				$shot->lesson_id = $lesson_id;
				$shot->asset_id = $new_asset_ids[$i];
				$shot->section = $new_sections[$i];
				$shot->shot = $new_shots[$i];
				$shot->type = $new_types[$i];
				$shot->script = $new_scripts[$i];
				$shot->script_english = $new_scripts_english[$i];
				$shot->create();
				$i++;
			}
		}
		
	}
	$assets = Task::find_all_assets_for_lesson($lesson->id);
	$shots = Shot::find_all_shots_for_lesson($lesson->id);
?>

<?php include_layout_template('header.php'); ?>

<div id="breadcrumbs" class="row">
	<ul class="breadcrumbs">
		<li><a href="lesson-db.php">Lesson DB</a></li>
		<li><a href="series.php?id=<?php echo $series->id; ?>"><?php echo $series->title; ?></a></li>
		<li>
			<a href="language-series.php?id=<?php echo $language_series->id; ?>">
				<?php echo $language_series->language_series_title." (".$language_series->level_code.")"; ?>
			</a>
		</li> 
		<li>
		  <a href="lesson.php?id=<?php echo $lesson->id; ?>"><?php echo $lesson->number.". ".$lesson->title; ?></a>
		</li>
		<li class="current">
			<a href="#">Script</a>
		</li>
	</ul>
</div>


<div class="row">
	<?php foreach($assets as $asset): ?>
		<div class="panel">
			<h3><?php echo $asset->task_name; ?></h3>
			<p>Total Shots: <?php echo count(Shot::find_all_shots_for_asset($asset->id)); ?></p>
			<p>Total Done: <?php echo count(Shot::find_all_completed_shots_for_asset($asset->id)); ?></p>
			<p><a href="record-asset.php?id=<?php echo $asset->id; ?>">Go to Recording Interface</a></p>
		</div>
	<?php endforeach; ?>
</div>

	<div id="script" class="row">
	<form action="lesson-script.php?id=<?php echo $lesson->id; ?>" method="post">
	<table class="script" id="lesson-script">
		<th>Asset</th><th>Section</th><th>Shot</th><th>Script</th><th>Script English</th>
		<?php foreach($shots as $shot): ?>
				<tr>
					<td>
						<input type="hidden" name="shot_id[]" value="<?php echo $shot->id; ?>">
						<select name="asset[]">
						<option value="0">not set</option>
						<?php foreach($assets as $asset) {
							echo "<option value='{$asset->id}' ";
							if($shot->asset_id == $asset->id) { echo "selected"; }
							echo ">{$asset->task_name}</option>";
						} ?>
					</td>
					<td><input type="text" name="section[]" size="10" value="<?php echo $shot->section; ?>"></td>
					<td>
						<input type="text" name="shot[]" size="3" value="<?php echo $shot->shot; ?>">
						<select name="type[]">
							<option value="">--</option>
							<option value="CU" <?php if($shot->type=="CU") { echo "selected"; } ?> >CU</option>
							<option value="WS" <?php if($shot->type=="WS") { echo "selected"; } ?> >WS</option>
							<option value="SWS" <?php if($shot->type=="SWS") { echo "selected"; } ?> >SWS</option>
							<option value="SCU" <?php if($shot->type=="SCU") { echo "selected"; } ?> >SCU</option>
						</select>
					</td>
					<td><textarea name="script[]" rows="15" cols="35"><?php echo $shot->script; ?></textarea></td>
					<td><textarea name="script_english[]" rows="15" cols="35"><?php echo $shot->script_english; ?></textarea></td>
				</tr>
			<?php endforeach; ?>
			<tr><td><span onclick="addRow()">+ Row</span></td></tr>
	</table>
	<input type="submit" name="edited_script" value="Submit Changes">
	</form>
	</div>
	<p><a href="language-series.php?series=<?php echo $lesson->series_id; ?>&id=<?php echo $lesson->language_series_id; ?>"><- Return to Language Series List</a></p>
	<script>
	newRowIndex = 0;
	
	function addRow() {
		var table = document.getElementById("lesson-script");
		var tableLength = document.getElementsByTagName("tr").length;
		var row = table.insertRow(tableLength-1);
		var cellAsset = row.insertCell(0);
		var cellSection = row.insertCell(1);
		var cellShot = row.insertCell(2);
		var cellScript = row.insertCell(3);
		var cellEnglish = row.insertCell(4);
		
		var inputAsset = document.createElement("select");
		inputAsset.name = "new_asset_id[]";
		
		<?php foreach($assets as $asset) { ?>
			var option = document.createElement("option");
			option.value = "<?php echo $asset->id; ?>";
			option.innerHTML = "<?php echo $asset->task_name; ?>";
			inputAsset.appendChild(option);
		<?php } ?>
		
		var inputId = document.createElement("input");
		inputId.type = "hidden";
		inputId.name = "new_ID[]";
		inputId.value = newRowIndex;
		
		var inputSection = document.createElement("input");
		inputSection.type = "text";
		inputSection.size = 10;
		inputSection.name = "new_section[]";
		
		var inputShot = document.createElement("input");
		inputShot.type = "text";
		inputShot.size = 3;
		inputShot.name = "new_shot[]";
		
		var inputType = document.createElement("select");
		inputType.name = "new_type[]";
		
		var option1 = document.createElement("option");
		option1.value = "";
		option1.innerHTML = "--";
		inputType.appendChild(option1);
		
		var option2 = document.createElement("option");
		option2.value = "CU";
		option2.innerHTML = "CU";
		inputType.appendChild(option2);
		
		var option3 = document.createElement("option");
		option3.value = "WS";
		option3.innerHTML = "WS";
		inputType.appendChild(option3);
		
		var option4 = document.createElement("option");
		option4.value = "SWS";
		option4.innerHTML = "SWS";
		inputType.appendChild(option4);
		
		var option5 = document.createElement("option");
		option5.value = "SCU";
		option5.innerHTML = "SCU";
		inputType.appendChild(option5);
		
		var inputScript = document.createElement("textarea");
		inputScript.rows = 15;
		inputScript.cols = 35;
		inputScript.name = "new_script[]";
		
		var inputEnglish = document.createElement("textarea");
		inputEnglish.rows = 15;
		inputEnglish.cols = 35;
		inputEnglish.name = "new_script_english[]";
		
		cellAsset.appendChild(inputId);
		cellAsset.appendChild(inputAsset);
		cellSection.appendChild(inputSection);
		cellShot.appendChild(inputShot);
		cellShot.appendChild(inputType);
		cellScript.appendChild(inputScript);
		cellEnglish.appendChild(inputEnglish);
		
		newRowIndex++;
	}
	
	</script>

<?php include_layout_template('footer.php'); ?>