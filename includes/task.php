<?php
require_once(LIB_PATH.DS.'database.php');

class Task extends DatabaseObject {
	protected static $table_name="task";
	protected static $db_view_fields = array('task.id' => 'id',
										'teamMember.nameFirst' => 'team_member_name',
										'taskGlobal.name' => 'task_name',
										'task.fkTeamMember' => 'team_member_id',
										'task.fkTaskGlobal' => 'global_task_id',
										'task.isCompleted' => 'is_completed',
										'task.isActive' => 'is_active',
										'task.timeActual' => 'time_actual',
										'IF(taskGlobal.isAsset=1, IF(task.isDelivered=1, taskGlobal.completionValue, 0), IF(task.isCompleted=1, taskGlobal.completionValue, 0))' => 'comp_value',
										'taskGlobal.actionableAt' => 'actionable_at',
										'series.id' => 'series_id',
										'series.title' => 'series_name',
										'languageSeries.id' => 'language_series_id',
										'lesson.id' => 'lesson_id',
										'language.name' => 'language_name',
										'lesson.title' => 'lesson_title',
										'lesson.number' => 'lesson_number',
										'level.code' => 'level_code',
										'timeActivate' => 'activated_time',
										'timeActual' => 'actual_time',
										'timeCompleted' => 'completed_time',
										'DATE_SUB(
									    LEAST(
  										  COALESCE(
  										    NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)
  										  ),
  										  COALESCE(
    										  NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0)
    										)
                      ), 
										  INTERVAL taskGlobal.dueDateOffset DAY
										)' => 'task_due_date',
										'task.isDelivered' => 'is_delivered',
										'taskGlobal.isAsset' => 'is_asset',
										'task.timeRunning' => 'time_running'
										);
										
	protected static $db_edit_fields = array('task.fkTeamMember' => 'team_member_id',
											'task.fkLesson' => 'lesson_id',
											'task.fkTaskGlobal' => 'global_task_id',
											'task.isCompleted' => 'is_completed',
											'task.isActive' => 'is_active',
											'task.timeActivate' => 'activated_time',
											'task.timeActual' => 'time_actual',
											'task.isDelivered' => 'is_delivered',
											'task.timeRunning' => 'time_running'
										);
										
	protected static $db_join_fields = array('teamMember' => 'task.fkTeamMember=teamMember.id',
											 'taskGlobal' => 'task.fkTaskGlobal=taskGlobal.id',
											 'lesson' => 'task.fkLesson=lesson.id',
											 'languageSeries' => 'lesson.fkLanguageSeries=languageSeries.id',
											 'series' => 'languageSeries.fkSeries=series.id',
											 'language' => 'languageSeries.fkLanguage=language.id',
											 'level' => 'languageSeries.fkLevel=level.id',
										 );
	
	public $id;
	public $task_name;
	public $task_due_date;
	public $is_completed;
	public $is_active;
	public $time_actual;
	public $comp_value;
	public $actionable_at;
	public $team_member_id;
	public $team_member_name;
	public $global_task_id;
	public $series_id;
	public $series_name;
	public $language_series_id;
	public $language_name;
	public $lesson_id;
	public $lesson_number;
	public $lesson_title;
	public $level_code;
	public $activated_time;
	public $running_time;
	public $actual_time;
	public $completed_time;
	public $is_delivered;
	public $is_asset;
	public $time_running;
	
	public static function find_all_assets_and_tasks_for_lesson($lesson_id) {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE task.fkLesson={$lesson_id} ";
		$sql .= "GROUP BY taskGlobal.id ";
		return static::find_by_sql($sql);
	}
		
	public static function find_all_tasks_for_lesson($lesson_id) {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE NOT taskGlobal.isAsset=1 ";
		$sql .= "AND task.fkLesson={$lesson_id} ";
		$sql .= "GROUP BY task.id ";
		return static::find_by_sql($sql);
	}
	
	public static function find_all_assets_for_lesson($lesson_id) {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE taskGlobal.isAsset=1 ";
		$sql .= "AND task.fkLesson={$lesson_id} ";
		$sql .= "GROUP BY task.id ";
		return static::find_by_sql($sql);
	}

	public static function get_actionable_assets_for_member($member_id) {
		// Assets for a lesson actionable at 0 only appear when at least one of the assets has been completed (but not necessarily delivered)
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE fkTeamMember = {$member_id} ";
		$sql .= "AND task.isActive=0 ";
		$sql .= "AND task.isCompleted=0 ";
		$sql .= "AND taskGlobal.isAsset=1 ";
		$sql .= "AND lesson.publishDateSite > 0 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "HAVING ( ( SELECT SUM( taskGlobal.completionValue ) FROM lesson sub_lesson JOIN task ON task.fkLesson=sub_lesson.id JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE task.isCompleted=1 AND lesson.id=sub_lesson.id ) >= taskGlobal.actionableAt ) OR taskGlobal.actionableAt = 0  ";
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(lesson.publishDateSite, INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC, language.name, series.title ";
		$sql .= "LIMIT 20 ";
		
		return static::find_by_sql($sql);
	}

	public static function get_actionable_tasks_for_member($member_id) {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE fkTeamMember = {$member_id} ";
		$sql .= "AND task.isActive=0 ";
		$sql .= "AND task.isCompleted=0 ";
		$sql .= "AND NOT taskGlobal.isAsset=1 ";
		$sql .= "AND LEAST( COALESCE(NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)), COALESCE(NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0))) > 0 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "HAVING ( ( SELECT SUM( taskGlobal.completionValue ) FROM lesson sub_lesson JOIN task ON task.fkLesson=sub_lesson.id JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE IF(taskGlobal.isAsset=1, task.isDelivered=1, task.isCompleted=1) AND lesson.id=sub_lesson.id ) >= taskGlobal.actionableAt ) OR taskGlobal.actionableAt = 0 ";
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(LEAST( COALESCE(NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)), COALESCE(NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0))), INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC, series.title ASC, language.name ASC, level.number ASC, lesson.number ASC ";
		$sql .= "LIMIT 10 ";
		
		return static::find_by_sql($sql);
	}
	
	public static function get_tasks_waiting_on_dropbox() {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE teamMember.isRemote = 1 ";
		$sql .= "AND lesson.timeUploadedDropbox < 1 ";
		$sql .= "AND NOT task.isCompleted = 1 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "HAVING ( ( SELECT SUM( taskGlobal.completionValue ) FROM lesson sub_lesson JOIN task ON task.fkLesson=sub_lesson.id JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE IF(taskGlobal.isAsset=1, task.isDelivered=1, task.isCompleted=1) AND lesson.id=sub_lesson.id ) >= taskGlobal.actionableAt ) OR taskGlobal.actionableAt = 0 ";
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(LEAST( COALESCE(NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)), COALESCE(NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0))), INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC, series.title ASC, language.name ASC, level.number ASC, lesson.number ASC ";
		
		return static::find_by_sql($sql);
	}
	
	public static function get_deliverable_assets_for_member($member_id) {
		// Get all assets marked as complete but not delivered for team member
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE fkTeamMember = {$member_id} ";
		$sql .= "AND task.isActive=0 ";
		$sql .= "AND task.isCompleted=1 ";
		$sql .= "AND NOT task.isDelivered=1 ";
		$sql .= "AND taskGlobal.isAsset=1 ";
		$sql .= "AND LEAST( COALESCE(NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)), COALESCE(NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0))) > 0 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "HAVING ( ( SELECT SUM( taskGlobal.completionValue ) FROM lesson sub_lesson JOIN task ON task.fkLesson=sub_lesson.id JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE IF(taskGlobal.isAsset=1, task.isDelivered=1, task.isCompleted=1) AND lesson.id=sub_lesson.id ) >= taskGlobal.actionableAt ) OR taskGlobal.actionableAt = 0 ";
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(LEAST( COALESCE(NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)), COALESCE(NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0))), INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC ";
		//$sql .= "LIMIT 10 ";
		
		return static::find_by_sql($sql);
	}
	
	public static function get_all_actionable_tasks($limit=FALSE) {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE NOT task.isActive=1 ";
		$sql .= "AND task.isCompleted=0 ";
		$sql .= "AND NOT taskGlobal.isAsset=1 ";
		$sql .= "AND LEAST( COALESCE(NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)), COALESCE(NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0))) > 0 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "HAVING ( ( SELECT SUM( taskGlobal.completionValue ) FROM lesson sub_lesson JOIN task ON task.fkLesson=sub_lesson.id JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE IF(taskGlobal.isAsset=1, task.isDelivered=1, task.isCompleted) AND lesson.id=sub_lesson.id ) >= taskGlobal.actionableAt )  OR taskGlobal.actionableAt = 0 ";
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(LEAST( COALESCE(NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)), COALESCE(NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0))), INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC ";
		if(is_int($limit)) {
  		$sql .= "LIMIT {$limit}";
		}
		return static::find_by_sql($sql);
	}
		
	public static function get_active_tasks_for_member($member_id) {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE fkTeamMember = {$member_id} ";
		$sql .= "AND task.isActive=1 ";
		$sql .= "AND task.isCompleted=0 ";
		$sql .= "AND NOT taskGlobal.isAsset=1 ";
		$sql .= "AND LEAST( COALESCE(NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)), COALESCE(NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0))) > 0 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(LEAST( COALESCE(NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)), COALESCE(NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0))), INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC ";
		$sql .= "LIMIT 10 ";
		
		return static::find_by_sql($sql);
	}
	
	public static function get_all_actionable_asset_tasks($limit=FALSE) {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE task.isActive=0 ";
		$sql .= "AND task.isCompleted=0 ";
		$sql .= "AND taskGlobal.isAsset=1 ";
		$sql .= "AND LEAST( COALESCE(NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)), COALESCE(NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0))) > 0 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "HAVING ( ( SELECT SUM( taskGlobal.completionValue ) FROM lesson sub_lesson JOIN task ON task.fkLesson=sub_lesson.id JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE task.isCompleted=1 AND lesson.id=sub_lesson.id ) >= taskGlobal.actionableAt ) OR taskGlobal.actionableAt = 0  ";
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(LEAST( COALESCE(NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)), COALESCE(NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0))), INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC ";
		if(is_int($limit)) {
  		$sql .= "LIMIT {$limit} ";
		}
		return static::find_by_sql($sql);
	}
	
	public static function get_all_deliverable_asset_tasks() {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE taskGlobal.isAsset=1 ";
		$sql .= "AND task.isCompleted=1 ";
		$sql .= "AND NOT task.isDelivered=1 ";
		$sql .= "AND LEAST( COALESCE(NULLIF(lesson.publishDateSite, 0), NULLIF(lesson.publishDateYouTube, 0)), COALESCE(NULLIF(lesson.publishDateYouTube, 0), NULLIF(lesson.publishDateSite, 0))) > 0 ";
		$sql .= "GROUP BY lesson_id ";
		$sql .= "ORDER BY task_due_date ASC ";
		//$sql .= "LIMIT 10 ";
		return static::find_by_sql($sql);
	}
	
	public static function get_all_active_tasks() {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE task.isActive=1 ";
		return static::find_by_sql($sql);
	}
	
	public static function find_all_completed_tasks_from_global_task($global_task_id) {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE task.fkTaskGlobal = {$global_task_id} ";
		$sql .= "AND task.isCompleted=1 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "ORDER BY task.fkTeamMember ";
		return static::find_by_sql($sql);
	}
	
	public static function get_recently_completed_tasks($limit=FALSE) {
		$period = $days_past;
		$since_time = date('Y-m-d H:i:s' , time() - ((60*60*60*60) * $period) );
	
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE task.isCompleted = 1 ";
		$sql .= "AND NOT taskGlobal.isAsset=1 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "ORDER BY task.timeCompleted DESC ";
		if(is_int($limit)) {
  		$sql .= "LIMIT {$limit}";
		}
		return static::find_by_sql($sql);
	}
	
	public static function get_recently_delivered_assets($limit=FALSE) {
		$period = $days_past;
		$since_time = date('Y-m-d H:i:s' , time() - ((60*60*60*60) * $period) );
	
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE task.isDelivered = 1 ";
		$sql .= "AND taskGlobal.isAsset=1 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "ORDER BY task.timeCompleted DESC ";
		if(is_int($limit)) {
  		$sql .= "LIMIT {$limit} ";
		}
		return static::find_by_sql($sql);
	}
	
	public static function get_recently_shot_lessons($limit=FALSE) {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE taskGlobal.isAsset=1 ";
		$sql .= "AND taskGlobal.name LIKE 'Shoot%' ";
		$sql .= "GROUP BY task.id ";
		$sql .= "ORDER BY task.timeCompleted DESC ";
    $sql .= "LIMIT 50 ";
		return static::find_by_sql($sql);
	}
	
	public function display_full_task_lesson() {
		echo $this->language_name ." ". $this->series_name . " (".strtoupper($this->level_code).") #" . $this->lesson_number;
	}
	
	public function display_full_task_lesson_task() {
		echo "<img src='images/{$this->level_code}.png'> ";
		echo "<a href='lesson.php?id=".$this->lesson_id."'>";
		echo $this->language_name . " - " . $this->series_name . " #" . $this->lesson_number . " - " . $this->task_name;
		echo "</a>";
	}
	
	public function activate_task() {
		global $database;
		$current_time = new DateTime(null, new DateTimeZone('UTC'));
	
		$sql  = "UPDATE task ";
		$sql .= "SET isActive=1 ";
		$sql .= ", timeActivate='{$current_time->format('Y-m-d H:i:s')}'" ;
		$sql .= "WHERE id={$this->id} ";
		
		$database->query($sql);
	}
	
	public function deactivate_task() {
		global $database;
		$current_time = new DateTime(null, new DateTimeZone('UTC'));
		$activated_time = new DateTime($this->activated_time, new DateTimeZone('UTC'));
		$this->deactivated_time = $current_time->format('Y-m-d H:i:s');
		$running_time = $this->time_running;
		$duration = $current_time->getTimestamp() - $activated_time->getTimestamp();
		$new_running_time = $running_time + $duration;
		
		$sql  = "UPDATE task ";
		$sql .= "SET isActive=0 ";
		$sql .= ", timeRunning='{$new_running_time}' ";
		$sql .= "WHERE id={$this->id} ";
		$sql .= "LIMIT 1";
		
		$database->query($sql);
		
		return "Running: ".seconds_to_timecode($running_time, 6)."<br />Duration: ".seconds_to_timecode($duration, 6)."<br />New Running: ".seconds_to_timecode($new_running_time, 6);
	}
	
	public function complete_task() {
		global $database;
		$current_time = new DateTime(null, new DateTimeZone('UTC'));
		$activated_time = new DateTime($this->activated_time, new DateTimeZone('UTC'));
		$running_time = $this->time_running;
		$duration = $current_time->getTimestamp() - $activated_time->getTimestamp();
		$time_actual = $running_time + $duration;
		
		$sql  = "UPDATE task ";
		$sql .= "SET isActive=0 ";
		$sql .= ", timeCompleted='{$current_time->format('Y-m-d H:i:s')}'";
		$sql .= ", timeActual='{$time_actual}' ";
		$sql .= ", isCompleted=1 ";
		$sql .= "WHERE id={$this->id} ";
		$sql .= "LIMIT 1";
		
		$database->query($sql);
		
		return "Running: ".seconds_to_timecode($running_time, 6)."<br />Duration: ".seconds_to_timecode($duration, 6)."<br />Time Actual: ".seconds_to_timecode($time_actual, 6);
	}

	public function complete_asset() {
		global $database;
		$current_time = new DateTime(null, new DateTimeZone('UTC'));

		// Then update
		$sql  = "UPDATE task ";
		$sql .= "SET isActive=0 ";
		$sql .= ", timeCompleted='{$current_time->format('Y-m-d H:i:s')}'";
		$sql .= ", isCompleted=1 ";
		$sql .= "WHERE id={$this->id} ";
		$sql .= "LIMIT 1";
		
		$database->query($sql);
	}

	public function deliver_asset() {
		global $database;
		$current_time = new DateTime(null, new DateTimeZone('UTC'));
		
		// Then update
		$sql  = "UPDATE task ";
		$sql .= "SET isActive=0 ";
		$sql .= ", isDelivered=1 ";
		$sql .= "WHERE id={$this->id} ";
		$sql .= "LIMIT 1";
		
		$database->query($sql);
	}
	
	public function complete_and_deliver_asset() {
		global $database;
		$current_time = new DateTime(null, new DateTimeZone('UTC'));
		
		// Then update
		$sql  = "UPDATE task ";
		$sql .= "SET isActive=0 ";
		$sql .= ", timeCompleted='{$current_time->format('Y-m-d H:i:s')}' ";
		$sql .= ", isCompleted=1 ";
		$sql .= ", isDelivered=1 ";
		$sql .= "WHERE id={$this->id} ";
		$sql .= "LIMIT 1";
		
		$database->query($sql);
	}
	
	public static function find_all() {
		return self::find_all_limit(0);
	}
}
?>