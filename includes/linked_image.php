<?php
require_once(LIB_PATH.DS.'database.php');

class Link extends DatabaseObject {
	protected static $table_name="link";
	protected static $db_view_fields = array('link.id' => 'id',
										'link.fkAsset' => 'asset_id',
										'link.text' => 'text',
										'link.url' => 'url'
										);
										
	protected static $db_edit_fields = array('link.id' => 'id',
											'link.fkAsset' => 'asset_id',
											'link.text' => 'text',
											'link.url' => 'url'
											);
										
	protected static $db_join_fields = array();
	
	public $id;
	public $asset_id;
	public $text;
	public $url;
	
	public static function get_links_for_asset($asset_id) {
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
		$sql .= "WHERE link.fkAsset = ".$asset_id." ";
		return static::find_by_sql($sql);
		}
}
?>