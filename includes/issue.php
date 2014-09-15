<?php
require_once(LIB_PATH.DS.'database.php');

class Issue extends DatabaseObject {
	protected static $table_name="taskComment";
	protected static $db_view_fields = array('taskComment.id' => 'id',
										'task.fkTeamMember' => 'team_member_id',
										'teamMember.nameFirst' => 'team_member_name',
										'taskComment.isCompleted' => 'is_completed',
										'taskComment.timeCompleted' => 'time_completed',
										'taskComment.body' => 'issue_body',
										'taskComment.creator' => 'issue_creator',
										'taskComment.timecode' => 'issue_timecode',
										'taskComment.fkTask' => 'task_id',
										'task.fkLesson' => 'lesson_id',
										'taskComment.fkActivity' => 'activity_id'
										);
										
	protected static $db_edit_fields = array('taskComment.isCompleted' => 'is_completed',
											'taskComment.timeCompleted' => 'time_completed',
											'taskComment.body' => 'issue_body',
											'taskComment.creator' => 'issue_creator',
											'taskComment.timecode' => 'issue_timecode',
											'taskComment.fkTask' => 'task_id',
											'taskComment.fkActivity' => 'activity_id'
											);
										
	protected static $db_join_fields = array('task' => 'taskComment.fkTask=task.id',
											 'teamMember' => 'task.fkTeamMember=teamMember.id',
											 'taskGlobal' => 'task.fkTaskGlobal=taskGlobal.id',
											 'lesson' => 'task.fkLesson=lesson.id',
											 'languageSeries' => 'lesson.fkLanguageSeries=languageSeries.id',
											 'series' => 'languageSeries.fkSeries=series.id',
											 'language' => 'languageSeries.fkLanguage=language.id',
											 'level' => 'languageSeries.fkLevel=level.id'
											 );
	
	public $id;
	public $team_member_id;
	public $team_member_name;
	public $is_completed;
	public $time_completed;
	public $time_actual;
	public $issue_body;
	public $issue_creator;
	public $issue_timecode;
	public $issue_reply;
	public $task_id;
	public $lesson_id;
	public $activity_id;
	
	public static function get_unfinished_issues_for_member($member_id) {
		
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
		$sql .= "WHERE task.fkTeamMember = ".$member_id." ";
		$sql .= "AND NOT taskComment.isCompleted=1 ";
		return static::find_by_sql($sql);
	}
	
	public static function get_unfinished_issues_for_lesson($lesson_id) {
		
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
		$sql .= "WHERE task.fkLesson = ".$lesson_id." ";
		$sql .= "AND taskComment.isCompleted=0 ";
		$sql .= "ORDER BY taskComment.timecode ";
		return static::find_by_sql($sql);
	}
	
	public static function get_all_issues_for_lesson($lesson_id) {
		
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
		$sql .= "WHERE task.fkLesson = ".$lesson_id." ";
		$sql .= "ORDER BY taskComment.timecode ";
		return static::find_by_sql($sql);
	}

	public static function get_all_unfinished_issues($limit = FALSE) {
		$sql  = "SELECT ";		
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "LEFT JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE NOT taskComment.isCompleted=1 ";
		if($limit) {
  		$sql .= "LIMIT {$limit} ";
		}
		return static::find_by_sql($sql);
	}
	
	public static function get_recently_completed_issues($days_past) {
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
		$sql .= "WHERE taskComment.isCompleted=1 ";
		$sql .= "AND DATE(taskComment.timeCompleted) > CURDATE() - INTERVAL {$period} DAY ";
		$sql .= "ORDER BY taskComment.timeCompleted DESC ";
		return static::find_by_sql($sql);
	}
	
	public function complete_issue($activity_id = NULL) {
		global $database;
		$current_time = new DateTime(null, new DateTimeZone('UTC'));

		// Then update
		$sql  = "UPDATE taskComment ";
		$sql .= "SET isCompleted=1 ";
		$sql .= ", timeCompleted='{$current_time->format('Y-m-d H:i:s')}'" ;
		if($activity_id) {
  		$sql .= ", fkActivity={$activity_id} ";
		}
		$sql .= "WHERE id={$this->id} ";
		$sql .= "LIMIT 1";
		
		$database->query($sql);
	}
}
?>