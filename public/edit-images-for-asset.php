<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	if($_POST['url']) {
		$asset = Task::find_by_id($_GET['id']);
		$images = Link::get_links_for_asset($asset->id);
		if($_POST['images_exist'] == 'yes') {
			// This is an edit
			$i = 0;
			$messages = "Links updated";
			foreach($images as $image) {
				$url =$db->escape_value($_POST['url'][$i]);
				$text = $db->escape_value($_POST['text'][$i]);
				$image->url = $url;
				$image->text = $text;
				$image->update();
				$i++;
			}
		} else {
			// These are new records
			$messages = "Links added";
			for($i=0; $i<=4; $i++) {
				$url = $db->escape_value($_POST['url'][$i]);
				$text = $db->escape_value($_POST['text'][$i]);
				$new_image = new Link();
				$new_image->asset_id = $asset->id;
				$new_image->url = $url;
				$new_image->text = $text;
				$new_image->create();
			}
		}	
	}
	$asset = Task::find_by_id($_GET['id']);
	$images = Link::get_links_for_asset($asset->id);
?>

<?php include_layout_template('header.php'); ?>
	<div>
		<?php if($messages) {
			echo "<p>{$messages}</p>";
		} ?>
		<?php $errors = $session->errors(); ?>
		<?php echo $session->form_errors($errors); ?>
		<?php if($images) {
			echo "<h2>Edit Images For ";
			echo $asset->display_full_task_lesson()."</h2>";
		} else {
			echo "<h2>New Images For ";
			echo $asset->display_full_task_lesson()."</h2>";
		} ?>
		
		<form action="edit-images-for-asset.php?id=<?php echo $asset->id;?>" method="POST">
			<?php
			if($images) {
				foreach($images as $image) {
					echo "<p>URL: <input name='url[]' type='text' size=80 value='".$image->url."' /><br />Text: <input name='text[]' type='text' size=80 value='".$image->text."'/></p>";
				}
			} else {
				for($i=0; $i<=4; $i++) {
					echo "<p>URL: <input name='url[]' type='text' size=80 /><br />Text: <input name='text[]' type='text' size=80 /></p>";
				}
			}?>
			<input type="hidden" name="images_exist" value="<?php if($images) { echo 'yes'; }?>">
			<p><input type="submit" name="edit_images" id="edit_images"></p>
		</form>
	</div>
<?php include_layout_template('footer.php'); ?>