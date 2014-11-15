<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	$channels = Channel::find_all_channels();
?>

<?php include_layout_template('header.php'); ?>

  <div class="small-12 medium-8 medium-centered columns">
  	<div id="breadcrumbs" class="row">
  		<ul class="breadcrumbs">
  			<li class="current"><a href="#">YouTube Channels</a></li> 
  		</ul>
  	</div>
		
		<div class="row">
  		  <h3>YouTube Channels</h3>
		</div>
		<?php if($channels) { ?>
		
		<div id="task-list-table" class="row">
		  <ol class="group">
				<?php foreach($channels as $channel): ?>
				<div class="group-item">
          <div class="group-item-body">
				    <div class="group-item-content">
  				    <div class="lesson-info">
        				<a class="lesson-title" href="channel.php?id=<?php echo $channel->id; ?>"><?php echo $channel->name; ?></a>
              </div>
              <div class="group-item-metadata">
                <p>Publishing Schedule: 
                  <?php
                    if($channel->publishSun == 1) {
                      echo "S"; } else { echo "-"; }
                    if($channel->publishMon == 1) {
                      echo "M"; } else { echo "-"; }
                    if($channel->publishTue == 1) {
                      echo "T"; } else { echo "-"; }
                    if($channel->publishWed == 1) {
                      echo "W"; } else { echo "-"; }
                    if($channel->publishThu == 1) {
                      echo "T"; } else { echo "-"; }
                    if($channel->publishFri == 1) {
                      echo "F"; } else { echo "-"; }
                    if($channel->publishSat == 1) {
                      echo "S"; } else { echo "-"; }
                  ?>
                </p>
              </div>
				    </div>
          </div>
    		</div>
    		<?php endforeach; ?>
			</ol>
		</div>
    <?php } ?>
    
<?php include_layout_template('footer.php'); ?>