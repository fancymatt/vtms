<?php
require_once(LIB_PATH.DS.'database.php');

class GlobalTask extends DatabaseObject {
	protected static $table_name="taskGlobal";
	protected static $db_view_fields = array('taskGlobal.id' => 'id',
										'taskGlobal.name' => 'task_name',
										'taskGlobal.actionableAt' => 'actionable_at',
										'taskGlobal.completionValue' => 'completion_value',
										'taskGlobal.fkSeries' => 'series_id',
										'taskGlobal.defaultTeamMember' => 'default_team_member_id',
										'taskGlobal.canAddIssues' => 'can_add_issues',
										'taskGlobal.issueReportingFriendlyText' => 'issue_reporting_friendly_text',
										'taskGlobal.isAsset' => 'is_asset',
										'taskGlobal.dueDateOffset' => 'due_date_offset',
										'taskGlobal.tutorialYtUrl' => 'tutorial_yt_url',
										'taskGlobal.tutorialShortDescription' => 'tutorial_description',
										'series.title' => 'series_name'
										);
										
	protected static $db_edit_fields = array('taskGlobal.name' => 'task_name',
										'taskGlobal.actionableAt' => 'actionable_at',
										'taskGlobal.completionValue' => 'completion_value',
										'taskGlobal.fkSeries' => 'series_id',
										'taskGlobal.defaultTeamMember' => 'default_team_member_id',
										'taskGlobal.isAsset' => 'is_asset',
										'taskGlobal.canAddIssues' => 'can_add_issues',
										'taskGlobal.issueReportingFriendlyText' => 'issue_reporting_friendly_text',
										'taskGlobal.dueDateOffset' => 'due_date_offset',
										'taskGlobal.tutorialYtUrl' => 'tutorial_yt_url',
										'taskGlobal.tutorialShortDescription' => 'tutorial_description'
										);
										
	protected static $db_join_fields = array('series' => 'taskGlobal.fkSeries=series.id');
	
	public $id;
	public $task_name;
	public $series_id;
	public $series_name;
	public $actionable_at;
	public $completion_value;
	public $default_team_member_id;
	public $default_team_member_name;
	public $can_add_issues;
	public $issue_reporting_friendly_text;
	public $is_asset;
	public $due_date_offset;
	public $tutorial_yt_url;
	public $tutorial_description;
	
	public static function find_all_tasks_for_lesson($lesson_id) {
		$child_table_name = "task";
		$parent_table_name = "Lesson";
		$sort_sql = "GROUP BY taskGlobal.pkTaskGlobal ORDER BY taskGlobal.actionableAt ASC";
		return self::find_all_child_for_parent($lesson_id, $child_table_name, $parent_table_name, $sort_sql);
	}
	
	public static function find_all_asset_tasks_for_series($series_id) {
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
		$sql .= "WHERE taskGlobal.isAsset = 1 ";
		$sql .= "AND taskGlobal.fkSeries = {$series_id} ";
		$sql .= "ORDER BY taskGlobal.actionableAt ";
		return static::find_by_sql($sql);
	}
	
	public static function get_all_global_tasks_for_series($series_id) {
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
		$sql .= "WHERE NOT taskGlobal.isAsset = 1 ";
		$sql .= "AND taskGlobal.fkSeries = {$series_id} ";
		$sql .= "ORDER BY taskGlobal.actionableAt ";
		return static::find_by_sql($sql);
	}
	
	public static function get_all_global_assets_and_tasks_for_series($series_id) {
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
		$sql .= "WHERE taskGlobal.fkSeries = {$series_id} ";
		$sql .= "ORDER BY taskGlobal.actionableAt ";
		return static::find_by_sql($sql);
	}
	
}
	
?>