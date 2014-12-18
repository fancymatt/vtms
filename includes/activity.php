<?php
require_once(LIB_PATH.DS.'database.php');

class Activity extends DatabaseObject {
	protected static $table_name="activity";
	protected static $db_view_fields = array('activity.id' => 'id',
                        										'activity.fkShift' => 'shift_id',
                        										'activity.fkTask' => 'task_id',
                        										'shift.fkTeamMember' => 'member_id',
                        										'teamMember.nameFirst' => 'member_name',
                        										'activity.isActive' => 'is_active',
                        										'activity.isCompleted' => 'is_completed',
                        										'activity.timeStart' => 'time_start',
                        										'activity.timeEnd' => 'time_end',
                        										'activity.activity' => 'activity',
                        										'task.fkLesson' => 'lesson_id',
                        										'(SELECT COUNT(taskComment.id) 
                        										 FROM taskComment 
                        										 WHERE taskComment.fkActivity=activity.id
                                             )' => 'issues_fixed');
										
	protected static $db_edit_fields = array('activity.fkShift' => 'shift_id',
										'activity.fkTask' => 'task_id',
										'activity.isActive' => 'is_active',
										'activity.isCompleted' => 'is_completed',
										'activity.timeStart' => 'time_start',
										'activity.timeEnd' => 'time_end',
										'activity.activity' => 'activity'
										);
										
	protected static $db_join_fields = array('shift' => 'shift.id=activity.fkShift',
	                                         'teamMember' => 'shift.fkTeamMember=teamMember.id',
	                                         'task' => 'activity.fkTask=task.id');
											
	public $id;
	public $shift_id;
	public $task_id;
	public $lesson_id;
	public $member_id;
	public $member_name;
	public $is_active;
	public $is_completed;
	public $time_start;
	public $time_end;
	public $activity;
	public $issues_fixed;
	
	public static function find_all() {
		return self::find_all_limit(0);
	}
	
	public static function find_all_recent_activities() {
		$sql  = "SELECT ";
		$i = 0;
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "LEFT JOIN ".$k." ON ".$v." ";
		}
		$sql .= "ORDER BY shift.time_end DESC";
		return static::find_by_sql($sql);
	}
	
	public static function get_active_activity_for_member($member_id) {
		$sql  = "SELECT ";
		$i = 0;
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "LEFT JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE shift.fkTeamMember = {$member_id} ";
		$sql .= "AND activity.isActive = 1 ";
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function find_all_activities_for_shift($shift_id) {
		$sql  = "SELECT ";
		$i = 0;
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "LEFT JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE activity.fkShift={$shift_id} ";
		$sql .= "ORDER BY activity.timeStart ASC ";
		return static::find_by_sql($sql);
	}
	
	public static function find_all_activities_for_lesson($lesson_id) {
		$sql  = "SELECT ";
		$i = 0;
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		foreach (self::$db_join_fields as $k => $v) {
			$sql .= "LEFT JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE task.fkLesson={$lesson_id} ";
		$sql .= "ORDER BY activity.timeStart ASC ";
		return static::find_by_sql($sql);
	}
}
?>