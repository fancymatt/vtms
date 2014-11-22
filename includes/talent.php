<?php
require_once(LIB_PATH.DS.'database.php');

class Talent extends DatabaseObject {
	protected static $table_name="talent";
	protected static $db_view_fields = array('talent.id' => 'id', 
										'talent.nameFirst' => 'name_first',
										'talent.nameLast' => 'name_last',
										'talent.isMale' => 'is_male'
										);
	protected static $db_join_fields=array();
	
	public $id;
	public $name_first;
	public $name_last;
	public $is_male;
		
	public static function find_all() {
		$sql  = "SELECT ";		
		foreach (static::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(static::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".static::$table_name." ";
		foreach (static::$db_join_fields as $k => $v) {
			$sql .= "JOIN ".$k." ON ".$v." ";
		}
		$sql .= "ORDER BY talent.nameLast ASC ";
		return self::find_by_sql($sql);
	}
	
}
?>