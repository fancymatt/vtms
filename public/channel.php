<?php require_once("../includes/initialize.php"); ?>
<?php	
	confirm_logged_in();
	$channel = Channel::find_by_id($db->escape_value($_GET['id']));
	$current_time = new DateTime(null, new DateTimeZone('UTC'));
	
	if($_POST['edited_channel_info']) {
  	$channel->publishSun = $_POST['publish_sun'];
  	$channel->publishMon = $_POST['publish_mon'];
  	$channel->publishTue = $_POST['publish_tue'];
  	$channel->publishWed = $_POST['publish_wed'];
  	$channel->publishThu = $_POST['publish_thu'];
  	$channel->publishFri = $_POST['publish_fri'];
  	$channel->publishSat = $_POST['publish_sat'];
  	$channel->update();
  	$_SESSION['message'] = "Lesson details updated.";
  	redirect_to("channel.php?id=".$channel->id);
	}
?>

<?php $page_title = "YT: ".ucwords($channel->name); ?>

<?php include_layout_template('header.php'); ?>
	<div class="small-12 medium-8 medium-centered columns">
  	<div id="breadcrumbs" class="row">
  		<ul class="breadcrumbs">
  			<li><a href="channels.php">YouTube Channels</a></li>
  			<li class="current"><a href="#"><?php echo $channel->name; ?></a></li> 
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
		<h4><?php echo "YouTube Channel: " . $channel->name; ?></h4>
    	<div class="task-sheet-tabs">
    		<ul class="tabs" data-tab>
    			<li class="tab-title active"><a href="#panel-schedule">Schedule</a></li>
    			<li class="tab-title"><a href="#panel-edit">Edit</a></li>
    		</ul>
    	</div>
    	
    	<div class="tabs-content">
    		<div class="content active" id="panel-schedule">
        	<div class="panel">
          	<h3>Schedule</h3>
          	<ul>
          	<?php
            	$days_backward = 10;
            	$days_forward = 60;
            	$today = $current_time;
            	
            	$days = array();
            	if($channel->publishSun) { $days[] = 'Sunday'; }
            	if($channel->publishMon) { $days[] = 'Monday'; }
            	if($channel->publishTue) { $days[] = 'Tuesday'; }
            	if($channel->publishWed) { $days[] = 'Wednesday'; }
            	if($channel->publishThu) { $days[] = 'Thursday'; }
            	if($channel->publishFri) { $days[] = 'Friday'; }
            	if($channel->publishSat) { $days[] = 'Saturday'; }
              
              $today = $today->sub(new DateInterval("P".$days_backward."D"));
            	for($i = 0; $i < ($days_forward + $days_backward); $i++) {
              	$today = $today->add(new DateInterval("P1D"));
              	if(in_array($today->format("l"), $days)) { 
                	$lessons_for_day = Lesson::find_lesson_for_youtube_publish_date($today->format('Y-m-d'), $channel->id);
              	  ?>
                	<li><?php echo $today->format("l, Y-m-d"); ?></li>
                	  <ul>
                	<?php
                  	if($lessons_for_day) {
                    	foreach($lessons_for_day as $lesson_for_day) {
                        echo "<li>";
                        echo $lesson_for_day->display_full_lesson();
                        echo "</li>";
                  	  }
                    } else {
                      echo "<li>No lesson scheduled</li>";
                	  } ?>
                	  </ul>
                  <?php }
                } ?>
            </ul>
        	</div>
    		</div>
    		<div class="content" id="panel-edit">
      		<div class="panel">
        		<h3>Edit</h3>
        		<form action="channel.php?id=<?php echo $channel->id; ?>" method="post">
          		<label for="publish_sun"><input type="checkbox" value="1" name="publish_sun" <?php if($channel->publishSun) { echo "checked"; } ?>>Sunday</label>
          		<label for="publish_mon"><input type="checkbox" value="1" name="publish_mon" <?php if($channel->publishMon) { echo "checked"; } ?>>Monday</label>
          		<label for="publish_tue"><input type="checkbox" value="1" name="publish_tue" <?php if($channel->publishTue) { echo "checked"; } ?>>Tuesday</label>
          		<label for="publish_wed"><input type="checkbox" value="1" name="publish_wed" <?php if($channel->publishWed) { echo "checked"; } ?>>Wednesday</label>
          		<label for="publish_thu"><input type="checkbox" value="1" name="publish_thu" <?php if($channel->publishThu) { echo "checked"; } ?>>Thursday</label>
          		<label for="publish_fri"><input type="checkbox" value="1" name="publish_fri" <?php if($channel->publishFri) { echo "checked"; } ?>>Friday</label>
          		<label for="publish_sat"><input type="checkbox" value="1" name="publish_sat" <?php if($channel->publishSat) { echo "checked"; } ?>>Saturday</label>
          		<input type="submit" name="edited_channel_info" value="Edit" class="action button">
        		</form>
      		</div>
    		</div>
  </div>
  	
	
<?php include_layout_template('footer.php'); ?>