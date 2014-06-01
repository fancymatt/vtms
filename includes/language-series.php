<?php
require_once(LIB_PATH.DS.'database.php');

class LanguageSeries extends DatabaseObject {
	protected static $table_name="languageSeries";
	
	protected static $db_edit_fields = array('seriesTitle' => 'language_series_title',
										'fkSeries' => 'series_id',
										'fkLanguage' => 'language_id',
										'fkLevel' => 'level_id', 
										);
	
	protected static $db_view_fields = array('languageSeries.id' => 'id', 
										'languageSeries.seriesTitle' => 'language_series_title',
										'language.id' => 'language_id', 
										'language.name' => 'language_name', 
										'level.id' => 'level_id', 
										'level.name' => 'level_name', 
										'level.code' => 'level_code',
										'series.id' => 'series_id', 
										'series.title' => 'series_name',
										'SEC_TO_TIME((SELECT SUM(lesson.trt) FROM lesson WHERE lesson.fkLanguageSeries=languageSeries.id))' => 'total_trt'
										);
	protected static $db_join_fields=array("language" => "language.id=languageSeries.fkLanguage", 
											"series" => "series.id=languageSeries.fkSeries", 
											"level" => "level.id=languageSeries.fkLevel"
											);
	public $id;
	public $language_id;
	public $language_series_title;
	public $language_name;
	public $level_id;
	public $level_name;
	public $level_code;
	public $series_id;
	public $series_name;
	public $total_trt;
	
	public static function find_all_language_series_for_series($series_id) {
		$child_table_name = "languageSeries";
		$parent_table_name = "series";
		$group_by_sql = "GROUP BY id ORDER BY language.name ASC, level.id ASC";
		return self::find_all_child_for_parent($series_id, $child_table_name, $parent_table_name, $group_by_sql);
	}
	
	public static function get_language_series_title_from_id($language_series_id) {
		$series = LanguageSeries::find_by_id($language_series_id);
		return $series->language_series_title;
	}
	
	public function display_full_language_series() {
		echo "<img src='images/{$this->level_code}.png'> ";
		echo "<a href='language-series.php?series=".$this->series_id."&id=".$this->id."'>";
		echo $this->language_series_title;
		echo "</a>";
	}
	
}
?>