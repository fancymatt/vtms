<?php
require_once(LIB_PATH.DS.'database.php');

class Shift extends DatabaseObject {
	protected static $table_name="shift";
	protected static $db_view_fields = array('shift.id' => 'id',
										'shift.fkTeamMember' => 'team_member_id',
										'shift.isActive' => 'is_active',
										'shift.clockIn' => 'clock_in',
										'shift.clockOut' => 'clock_out'
										);
										
	protected static $db_edit_fields = array('shift.id' => 'id',
										'shift.fkTeamMember' => 'team_member_id',
										'shift.isActive' => 'is_active',
										'shift.clockIn' => 'clock_in',
										'shift.clockOut' => 'clock_out'
										);
										
	protected static $db_join_fields = array();
											
	public $id;
	public $team_member_id;
	public $is_active;
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
	
	public static function get_active_shift_for_member($member_id) {
		$sql  = "SELECT ";
		$i = 0;
		foreach (self::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(self::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".self::$table_name." ";
		$sql .= "WHERE shift.fkTeamMember = {$member_id} ";
		$sql .= "AND shift.isActive = '1' ";
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function clock_in_team_member($team_member_id) {
		global $database;
		$current_time = new DateTime(null, new DateTimeZone('UTC'));
	
		$sql  = "INSERT INTO shift (fkTeamMember, isActive, clockIn) ";
		$sql .= "VALUES ('{$team_member_id}', '1', '{$current_time->format('Y-m-d H:i:s')}') ";
		
		$database->query($sql);
	}
	
	public function clock_out_team_member($team_member_id) {
		global $database;
		$current_time = new DateTime(null, new DateTimeZone('UTC'));
		
		$sql  = "UPDATE shift ";
		$sql .= "SET isActive=0 ";
		$sql .= ", clockOut='{$current_time->format('Y-m-d H:i:s')}' ";
		$sql .= "WHERE id={$this->id} ";
		$sql .= "LIMIT 1";
		
		return $database->query($sql);
	}
}
?>