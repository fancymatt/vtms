<?php
require_once(LIB_PATH.DS.'database.php');

class Shift extends DatabaseObject {
	protected static $table_name="shift";
	protected static $db_view_fields = array('shift.id' => 'id',
										'shift.fkTeamMember' => 'team_member_id',
										'shift.clockIn' => 'clock_in',
										'shift.clockOut' => 'clock_out'
										);
										
	protected static $db_edit_fields = array('shift.id' => 'id',
										'shift.fkTeamMember' => 'team_member_id',
										'shift.clockIn' => 'clock_in',
										'shift.clockOut' => 'clock_out'
										);
										
	protected static $db_join_fields = array();
											
	public $id;
	public $team_member_id;
	public $clock_in;
	public $clock_out;
	
	public static function find_all() {
		return self::find_all_limit(0);
	}
	
	public static function find_all_recent_shifts() {
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
		$sql .= "ORDER BY shift.clockIn DESC";
		return static::find_by_sql($sql);
	}
}
?>