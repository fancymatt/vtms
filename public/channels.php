<?php require_once("../includes/initialize.php"); ?>
<?php
	confirm_logged_in();
	
	$days_backward = 5;
	$days_forward = 30;
	
	$current_time = new DateTime(null, new DateTimeZone('UTC'));
	$today = $current_time->sub(new DateInterval("P".$days_backward."D"));

	$channels = Channel::find_all_channels();
?>

<?php include_layout_template('header.php'); ?>
	<div class="small-12 medium-8 medium-centered columns">
  	<div id="breadcrumbs" class="row">
  		<ul class="breadcrumbs">
  			<li class="current"><a href="#">YouTube Channels</a></li> 
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
  	<div class="task-sheet-tabs">
  		<ul class="tabs" data-tab>
  			<li class="tab-title active"><a href="#panel-schedule">Schedule</a></li>
  			<li class="tab-title"><a href="#panel-channels">Channels</a></li>
  		</ul>
  	</div>

  	<div class="tabs-content">
  		<div class="content" id="panel-channels">
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
  		</div>
  		<div class="content active" id="panel-schedule">
    		<ol class="group">
        <?php
        for($i = 0; $i < ($days_forward + $days_backward); $i++) {
          
          $today = $today->add(new DateInterval("P1D"));
          
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
          
          foreach($channels as $channel) {
            
            $days = array();
            if($channel->publishSun) { $days[] = 'Sunday'; }
            if($channel->publishMon) { $days[] = 'Monday'; }
            if($channel->publishTue) { $days[] = 'Tuesday'; }
            if($channel->publishWed) { $days[] = 'Wednesday'; }
            if($channel->publishThu) { $days[] = 'Thursday'; }
            if($channel->publishFri) { $days[] = 'Friday'; }
            if($channel->publishSat) { $days[] = 'Saturday'; }
            
            if(in_array($today->format("l"), $days)) { ?>
              
              <div class="activity">
          		  <a href="channel.php?id=<?php echo $channel->id; ?>"><?php echo $channel->name; ?></a>:
                  <?php
                  $lessons_for_day = Lesson::find_lesson_for_youtube_publish_date($today->format('Y-m-d'), $channel->id);
                  if($lessons_for_day) {
                    foreach($lessons_for_day as $lesson_for_day) { ?>
                      <strong>
                      <a href="lesson.php?id=<?php echo $lesson_for_day->id; ?>"><?php $lesson_for_day->display_full_lesson(); ?></a>
                      </strong>
                      <?php 
                      if($lesson_for_day->checked_language == 1) { echo "lan"; } else { echo "---"; }
                      echo " | ";
                      if($lesson_for_day->checked_video == 1) { echo "vid"; } else { echo "---"; }
                      echo " | ";
                      if($lesson_for_day->files_moved == 1) { echo "arc"; } else { echo "---"; }
                      ?>
                      <?php                  
                    }
                  } else {
                    echo "Empty ";
                  } ?>
              </div>
                  <?php } ?>              
            <?php } ?>
          
            </div>
          </div>
          <?php } ?>
          
          </div>
    		</ol>
      </div>
    
<?php include_layout_template('footer.php'); ?>