<?php
require_once(LIB_PATH.DS.'database.php');

class Channel extends DatabaseObject {
	protected static $table_name="channel";
	
	protected static $db_view_fields = array('channel.id' => 'id',
                      										 'channel.name' => 'name',
                      										 'channel.url' => 'url',
                      										 'channel.publishSun' => 'publishSun',
                      										 'channel.publishMon' => 'publishMon',
                      										 'channel.publishTue' => 'publishTue',
                      										 'channel.publishWed' => 'publishWed',
                      										 'channel.publishThu' => 'publishThu',
                      										 'channel.publishFri' => 'publishFri',
                      										 'channel.publishSat' => 'publishSat'
                      										 );
	
	protected static $db_edit_fields = array('channel.name' => 'name',
                      										 'channel.url' => 'url',
                      										 'channel.publishSun' => 'publishSun',
                      										 'channel.publishMon' => 'publishMon',
                      										 'channel.publishTue' => 'publishTue',
                      										 'channel.publishWed' => 'publishWed',
                      										 'channel.publishThu' => 'publishThu',
                      										 'channel.publishFri' => 'publishFri',
                      										 'channel.publishSat' => 'publishSat'
                      										 );
  
	protected static $db_join_fields = array();
	
	public $id;
	public $name;
	public $url;
	public $publishSun;
	public $publishMon;
	public $publishTue;
	public $publishWed;
	public $publishThu;
	public $publishFri;
	public $publishSat;
	
	public static function find_all_channels() {
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
		$sql .= "ORDER BY channel.name ";
		return static::find_by_sql($sql);
	}
	
}
?>