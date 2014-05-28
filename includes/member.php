<?php
require_once(LIB_PATH.DS.'database.php');

class Member extends DatabaseObject {
	protected static $table_name="teamMember";
	protected static $db_view_fields = array('teamMember.id' => 'id',
										'teamMember.nameFirst' => 'first_name',
										'teamMember.nameLast' => 'last_name',
										'teamMember.active' => 'active',
										'teamMember.isRemote' => 'remote',
										'user.id' => 'user_id'
										);
										
	protected static $db_edit_fields = array('teamMember.id' => 'id',
										'teamMember.nameFirst' => 'first_name',
										'teamMember.nameLast' => 'last_name',
										'teamMember.active' => 'active',
										'teamMember.isRemote' => 'remote'
										);
										
	protected static $db_join_fields = array('user' => 'teamMember.id=user.fkTeamMember');
											
	public $id;
	public $first_name;
	public $last_name;
	public $active;
	public $user_id;
	public $remote;
	
	public static function find_all() {
		return self::find_all_limit(0);
	}
	
	public static function find_all_members($only_active=YES) {
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
		if ($only_active) {
			$sql .= "WHERE active=1 ";
		}
		$sql .= "ORDER BY first_name ";
		return static::find_by_sql($sql);
	}
		
	public static function member_id_from_name($member_name) {
		$sql  = "SELECT id from teamMember ";
		$sql .= "WHERE nameFirst = '".$member_name."' ";
		$sql .= "LIMIT 1";
		
		$result_array = static::find_by_sql($sql);
		return $result_array['id'];
	}
}
?>