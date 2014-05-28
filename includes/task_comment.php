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
										'lesson.number' => 'lesson_number',
										'level.code' => 'level_code',
										'timeActivate' => 'activated_time',
										'timeActual' => 'actual_time',
										'timeCompleted' => 'completed_time',
										'DATE_SUB(DATE_SUB(lesson.publishDateSite, INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY)' => 'task_due_date',
										'task.isDelivered' => 'is_delivered',
										'taskGlobal.isAsset' => 'is_asset'
										);
										
	protected static $db_edit_fields = array('task.fkTeamMember' => 'team_member_id',
											'task.fkLesson' => 'lesson_id',
											'task.fkTaskGlobal' => 'global_task_id',
											'task.isCompleted' => 'is_completed',
											'task.isActive' => 'is_active',
											'task.timeActual' => 'time_actual',
											'task.isDelivered' => 'is_delivered'
										);
										
	protected static $db_join_fields = array('teamMember' => 'task.fkTeamMember=teamMember.id',
											 'taskGlobal' => 'task.fkTaskGlobal=taskGlobal.id',
											 'lesson' => 'task.fkLesson=lesson.id',
											 'languageSeries' => 'lesson.fkLanguageSeries=languageSeries.id',
											 'series' => 'languageSeries.fkSeries=series.id',
											 'language' => 'languageSeries.fkLanguage=language.pkLanguage',
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
	public $level_code;
	public $activated_time;
	public $running_time;
	public $actual_time;
	public $completed_time;
	public $is_delivered;
	public $is_asset;
	
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
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(lesson.publishDateSite, INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC ";
		$sql .= "LIMIT 10 ";
		
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
		$sql .= "AND lesson.publishDateSite > 0 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "HAVING ( ( SELECT SUM( taskGlobal.completionValue ) FROM lesson sub_lesson JOIN task ON task.fkLesson=sub_lesson.id JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE IF(taskGlobal.isAsset=1, task.isDelivered=1, task.isCompleted=1) AND lesson.id=sub_lesson.id ) >= taskGlobal.actionableAt ) OR taskGlobal.actionableAt = 0 ";
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(lesson.publishDateSite, INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC ";
		$sql .= "LIMIT 10 ";
		
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
		$sql .= "AND lesson.publishDateSite > 0 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "HAVING ( ( SELECT SUM( taskGlobal.completionValue ) FROM lesson sub_lesson JOIN task ON task.fkLesson=sub_lesson.id JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE IF(taskGlobal.isAsset=1, task.isDelivered=1, task.isCompleted=1) AND lesson.id=sub_lesson.id ) >= taskGlobal.actionableAt ) OR taskGlobal.actionableAt = 0 ";
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(lesson.publishDateSite, INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC ";
		$sql .= "LIMIT 10 ";
		
		return static::find_by_sql($sql);
	}
	
	public static function get_all_actionable_tasks() {
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
		$sql .= "AND lesson.publishDateSite > 0 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "HAVING ( ( SELECT SUM( taskGlobal.completionValue ) FROM lesson sub_lesson JOIN task ON task.fkLesson=sub_lesson.id JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE IF(taskGlobal.isAsset=1, task.isDelivered=1, task.isCompleted) AND lesson.id=sub_lesson.id ) >= taskGlobal.actionableAt )  OR taskGlobal.actionableAt = 0 ";
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(lesson.publishDateSite, INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC ";
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
		$sql .= "AND lesson.publishDateSite > 0 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(lesson.publishDateSite, INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC ";
		$sql .= "LIMIT 10 ";
		
		return static::find_by_sql($sql);
	}
	
	public static function get_all_actionable_asset_tasks() {
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
		$sql .= "AND lesson.publishDateSite > 0 ";
		$sql .= "GROUP BY task.id ";
		$sql .= "HAVING ( ( SELECT SUM( taskGlobal.completionValue ) FROM lesson sub_lesson JOIN task ON task.fkLesson=sub_lesson.id JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE task.isCompleted=1 AND lesson.id=sub_lesson.id ) >= taskGlobal.actionableAt ) OR taskGlobal.actionableAt = 0  ";
		$sql .= "ORDER BY DATE_SUB(DATE_SUB(lesson.publishDateSite, INTERVAL taskGlobal.dueDateOffset DAY), INTERVAL lesson.bufferLength DAY) ASC ";
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
		$sql .= "AND lesson.publishDateSite > 0 ";
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
		$sql .= "AND (SELECT SUM(taskGlobal.completionValue) FROM lesson) >= taskGlobal.actionableAt ";
		$sql .= "GROUP BY lesson_id ";
		$sql .= "ORDER BY task_due_date ASC ";
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
	
	public static function get_recently_completed_tasks($days_past=1) {
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
		$sql .= "AND DATE(task.timeCompleted) > CURDATE() - INTERVAL {$period} DAY ";
		$sql .= "GROUP BY task.id ";
		$sql .= "ORDER BY task.timeCompleted DESC ";
		return static::find_by_sql($sql);
	}
	
	public static function get_recently_delivered_assets($days_past=1) {
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
		$sql .= "AND DATE(task.timeCompleted) > CURDATE() - INTERVAL {$period} DAY ";
		$sql .= "GROUP BY task.id ";
		$sql .= "ORDER BY task.timeCompleted DESC ";
		return static::find_by_sql($sql);
	}
	
	public function display_full_task_lesson() {
		echo "<a href='viewLesson.php?series=".$this->series_id."&langSeries=".$this->language_series_id."&lesson=".$this->lesson_id."'>";
		echo $this->language_name . " - " . $this->series_name . " #" . $this->lesson_number;
		echo "</a>";
	}
	
	public function activate_task() {
		global $database;
		$current_time = date('Y-m-d H:i:s' , time());
	
		$sql  = "UPDATE task ";
		$sql .= "SET isActive=1 ";
		$sql .= ", timeActivate='{$current_time}'" ;
		$sql .= "WHERE id={$this->id} ";
		
		$database->query($sql);
	}
	
	public function deactivate_task() {
		global $database;
		$current_time = date('Y-m-d H:i:s' , time());
	
		$activated_time_unix = strtotime($task->activated_time);
		$current_time_unix = time();
		$elapsed_time = $current_time_unix - $activated_time_unix;
		$new_running_time = $task->running_time + $elapsed_time;
		
		// Then update
		$sql  = "UPDATE task ";
		$sql .= "SET isActive=0 ";
		$sql .= ", timeDeactivated='{$current_time}'" ;
		$sql .= ", timeRunning='{$new_running_time}' ";
		$sql .= "WHERE id={$this->id} ";
		$sql .= "LIMIT 1";
		
		$database->query($sql);
		
		return gmdate($running_time) . "+" . gmdate($elapsed_time) . "=" . gmdate('H:i:s', $new_running_time);
	}
	
	function complete_task() {
		global $database;
		$current_time = date('Y-m-d H:i:s' , time());
	
		$activated_time_unix = strtotime($task->activated_time);
		$current_time_unix = time();
		$elapsed_time = $current_time_unix - $activated_time_unix;
		$new_running_time = $task->running_time + $elapsed_time;
		
		// Then update
		$sql  = "UPDATE task ";
		$sql .= "SET isActive=0 ";
		$sql .= ", timeCompleted='{$current_time}'" ;
		$sql .= ", timeActual='{$new_running_time}' ";
		$sql .= ", isCompleted=1 ";
		$sql .= "WHERE id={$this->id} ";
		$sql .= "LIMIT 1";
		
		$database->query($sql);
		
		return gmdate($running_time) . "+" . gmdate($elapsed_time) . "=" . gmdate('H:i:s', $new_running_time);
	}

	function complete_asset() {
		global $database;
		$current_time = date('Y-m-d H:i:s' , time());
		
		// Then update
		$sql  = "UPDATE task ";
		$sql .= "SET isActive=0 ";
		$sql .= ", timeCompleted='{$current_time}'" ;
		$sql .= ", isCompleted=1 ";
		$sql .= "WHERE id={$this->id} ";
		$sql .= "LIMIT 1";
		
		$database->query($sql);
	}

	function deliver_asset() {
		global $database;
		$current_time = date('Y-m-d H:i:s' , time());
		
		// Then update
		$sql  = "UPDATE task ";
		$sql .= "SET isActive=0 ";
		$sql .= ", isDelivered=1 ";
		$sql .= "WHERE id={$this->id} ";
		$sql .= "LIMIT 1";
		
		$database->query($sql);
	}
	
	function complete_and_deliver_asset() {
		global $database;
		$current_time = date('Y-m-d H:i:s' , time());
		
		// Then update
		$sql  = "UPDATE task ";
		$sql .= "SET isActive=0 ";
		$sql .= ", isComplete=1 ";
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