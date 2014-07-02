<?php
require_once(LIB_PATH.DS.'database.php');

class Level extends DatabaseObject {
	protected static $table_name="level";
	protected static $db_view_fields = array('level.id' => 'id', 
										'level.name' => 'name',
										'level.code' => 'code', 
										'level.number' => 'number'
										);
	protected static $db_join_fields=array();
	
	public $id;
	public $name;
	public $code;
	public $number;
	
	public static function find_all() {
		return self::find_all_limit(0);
	}
}
?>