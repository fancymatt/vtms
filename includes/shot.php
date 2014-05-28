<?php
require_once(LIB_PATH.DS.'database.php');

class Shot extends DatabaseObject {
	protected static $table_name="shot";
	protected static $db_view_fields = array('shot.id' => 'id',
										'shot.fkLesson' => 'lesson_id',
										'shot.fkTalent' => 'talent_id',
										'shot.fkAsset' => 'asset_id',
										'shot.section' => 'section',
										'shot.shot' => 'shot',
										'shot.script' => 'script',
										'shot.scriptEnglish' => 'script_english',
										'shot.scriptVideo' => 'script_video',
										'shot.type' => 'type',
										'shot.speaker' => 'speaker'
										);
										
	protected static $db_edit_fields = array('shot.id' => 'id',
											'shot.fkLesson' => 'lesson_id',
											'shot.fkTalent' => 'talent_id',
											'shot.fkAsset' => 'asset_id',
											'shot.section' => 'section',
											'shot.shot' => 'shot',
											'shot.script' => 'script',
											'shot.scriptEnglish' => 'script_english',
											'shot.scriptVideo' => 'script_video',
											'shot.type' => 'type',
											'shot.speaker' => 'speaker'
											);
										
	protected static $db_join_fields = array( );
	
	public $id;
	public $lesson_id;
	public $talent_id;
	public $asset_id;
	public $section;
	public $shot;
	public $script;
	public $script_english;
	public $script_video;
	public $type;
	
	public static function find_all_shots_for_lesson($lesson_id) {
	global $db;
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
		$sql .= "WHERE shot.fkLesson={$lesson_id} ";
		$sql .= "GROUP BY id ";
		$sql .= "ORDER BY section, shot ";
		return static::find_by_sql($sql);
	}
}
?>