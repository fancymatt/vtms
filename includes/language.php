<?php
require_once(LIB_PATH.DS.'database.php');

class Language extends DatabaseObject {
	protected static $table_name="language";
	protected static $db_view_fields = array('language.id' => 'id', 
										'language.name' => 'name',
										'language.country' => 'country_name', 
										'language.code' => 'code', 
										'language.siteUrlShort' => 'site_url_short'
										);
	protected static $db_join_fields=array();
	
	public $id;
	public $name;
	public $country_name;
	public $code;
	public $site_url_short;
	
	// Any way to not repeat myself so much in these two functions?
	
	public static function find_by_id($id=0) {
		$sql  = "SELECT id";
		$sql .= ", name";
		$sql .= ", country AS country_name";
		$sql .= ", code";
		$sql .= ", siteUrlShort AS site_url_short ";
		$sql .= "FROM language ";
		$sql .= "WHERE id={$id} ";
		$sql .= "LIMIT 1 ";
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function find_all() {
		return self::find_all_limit(0);
	}
}
?>