<?php
require_once(LIB_PATH.DS.'database.php');

class GlobalTaskStatistic extends DatabaseObject {
	protected static $table_name="taskGlobal";
	protected static $db_view_fields = array('task.fkTeamMember' => 'team_member_id',
										'teamMember.nameFirst' => 'team_member_name',
										'COUNT(task.isCompleted = 1)' => 'times_completed',
										'FLOOR(AVG(NULLIF(task.timeActual, 0)))' => 'average_time'
										);
										
	protected static $db_edit_fields = array(
										);
										
	protected static $db_join_fields = array('task' => 'task.fkTaskGlobal=taskGlobal.id',
											'teamMember' => 'taskGlobal.defaultTeamMember=teamMember.id'
											);
	
	public $team_member_id;
	public $team_member_name;
	public $times_completed;
	public $average_time;
	
	public static function find_by_id($id=0) {
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
		$sql .= "WHERE ".static::$table_name.".id = ".$id." ";
		$sql .= "GROUP BY task.fkTeamMember ";
		$sql .= "ORDER BY task.timeActual ASC ";
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}
}
	
?>