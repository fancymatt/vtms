<?php require_once("../includes/initialize.php"); ?>
<?php	
	confirm_logged_in();
	$channel = Channel::find_by_id($db->escape_value($_GET['id']));
	$current_time = new DateTime(null, new DateTimeZone('UTC'));
	
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
  
  $properly_scheduled_lessons = array();
  
  $scheduled_lessons = Lesson::find_all_youtube_videos_scheduled_in_period($days_forward, $channel->id);
  $scheduled_lesson_ids = array();
  foreach($scheduled_lessons as $scheduled_lesson) {
    $scheduled_lesson_ids[] = $scheduled_lesson->id; 
  }
  
  $eligble_lessons = Lesson::find_all_eligible_youtube_lessons($channel->id);
	
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
    			<li class="tab-title"><a href="#panel-lessons">Lessons</a></li>
    			<li class="tab-title"><a href="#panel-edit">Edit</a></li>
    		</ul>
    	</div>
    	
    	<div class="tabs-content">
    		<div class="content active" id="panel-schedule">
          <h3 class="group-heading">Schedule</h3>
          <ol class="group">
            
            <?php
            for($i = 0; $i < ($days_forward + $days_backward); $i++) {
            $today = $today->add(new DateInterval("P1D"));
            if(in_array($today->format("l"), $days)) {
            $lessons_for_day = Lesson::find_lesson_for_youtube_publish_date($today->format('Y-m-d'), $channel->id);
            ?>
            
            <div class="group-item">
              <div class="member">
                <div class="member-image">
                  
                </div>
                <p class="member-name">
          				
                </p>
      				</div>
      				<div class="issue-info">
        				<p class="lesson-title"><?php echo $today->format("l, M jS"); ?></p>
      				</div>
      				<div class="activity-list">
              	<?php
              	if($lessons_for_day) {
                  	foreach($lessons_for_day as $lesson_for_day) {
                    	$properly_scheduled_lessons[] = $lesson_for_day->id;
                ?>
          		  <div class="activity">
            		  <a href="lesson.php?id=<?php echo $lesson_for_day->id; ?>">
              		  <strong><?php $lesson_for_day->display_full_lesson(); ?></strong>
            		  </a>      		  
            		</div>
            		<?php } ?>
            		<?php } else { ?>
            		<div class="activity">
            		  <a href="#">Nothing scheduled</a>     		  
            		</div>
            		<?php } ?>
        		  </div>
        		</div>
        		<?php } ?>
        		<?php } ?>
          </ol>
        </div>
        
        <div class="content" id="panel-lessons">
          <div class="panel">
            <h3>Improperly Scheduled Lessons</h3>
            <?php foreach($scheduled_lesson_ids as $scheduled_lesson_id) {
              if(!in_array($scheduled_lesson_id, $properly_scheduled_lessons)) {
                $lesson = Lesson::find_by_id($scheduled_lesson_id);
                $lesson_date = new DateTime(strftime($lesson->publish_date_yt)); 
                ?>
                <li>
                <a href="lesson.php?id=<?php echo $lesson->id; ?>">
                <?php echo $lesson->display_full_lesson(); ?>
                </a>
                - <?php echo $lesson_date->format("l, M jS"); ?>  
                </li>
                <?php
              }
            } ?>
          </div>
          <div class="panel">
            <h3>Eligible and Unscheduled Lessons Not on YouTube</h3>
            <?php foreach($eligble_lessons as $eligible_lesson) { 
              ?>
              <li>
              <a href="lesson.php?id=<?php echo $eligible_lesson->id; ?>">
              <?php echo $eligible_lesson->display_full_lesson(); ?>
              </a> - <?php echo $eligible_lesson->title; ?> 
              </li>
              <? } ?>
          </div>
        </div>
          
    		<div class="content" id="panel-edit">
      		<div class="panel">
        		<h3>Edit</h3>
        		<div class="small-6 columns">
          		<form action="channel.php?id=<?php echo $channel->id; ?>" method="post">
            		<label for="publish_sun"><input type="checkbox" value="1" name="publish_sun" <?php if($channel->publishSun) { echo "checked"; } ?>>Sunday</label>
            		<label for="publish_mon"><input type="checkbox" value="1" name="publish_mon" <?php if($channel->publishMon) { echo "checked"; } ?>>Monday</label>
            		<label for="publish_tue"><input type="checkbox" value="1" name="publish_tue" <?php if($channel->publishTue) { echo "checked"; } ?>>Tuesday</label>
            		<label for="publish_wed"><input type="checkbox" value="1" name="publish_wed" <?php if($channel->publishWed) { echo "checked"; } ?>>Wednesday</label>
            		<label for="publish_thu"><input type="checkbox" value="1" name="publish_thu" <?php if($channel->publishThu) { echo "checked"; } ?>>Thursday</label>
            		<label for="publish_fri"><input type="checkbox" value="1" name="publish_fri" <?php if($channel->publishFri) { echo "checked"; } ?>>Friday</label>
            		<label for="publish_sat"><input type="checkbox" value="1" name="publish_sat" <?php if($channel->publishSat) { echo "checked"; } ?>>Saturday</label>
            </div>
        		<div class="small-6 columns">
          		<label for="channel_name">Channel Name: <input type="text" value="<?php echo $channel->name; ?>" name="channel_name"></label>
          		<label for="channel_url">Channel URL: <input type="text" value="<?php echo $channel->url; ?>" name="channel_url"></label>
        		</div>
        		<div class="small-12 columns">
          		<input type="submit" name="edited_channel_info" value="Edit" class="action button">
          		</form>
            </div>
    		  </div>
        </div>
  	
	
<?php include_layout_template('footer.php'); ?>