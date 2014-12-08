<?php
require_once(LIB_PATH.DS.'database.php');

class Series extends DatabaseObject {
	protected static $table_name="series";
	protected static $db_view_fields = array('series.id' => 'id',
										'series.code' => 'code',
										'series.title' => 'title',
										'SEC_TO_TIME((SELECT SUM( lesson.trt ) FROM series s JOIN languageSeries ON s.id = languageSeries.fkSeries JOIN lesson ON lesson.fkLanguageSeries = languageSeries.id WHERE s.id = series.id))' => 'series_trt',
										'series.shotAt' => 'shot_at',
										'series.checkableAt' => 'checkable_at',
										'series.ytTitleTemplate' => 'yt_title_template',
										'series.ytDescriptionTemplate' => 'yt_description_template',
										'series.levelSignificant' => 'level_significant'
										);
										
	protected static $db_edit_fields = array('series.code' => 'code',
										'series.title' => 'title',
										'series.shotAt' => 'shot_at',
										'series.checkableAt' => 'checkable_at',
										'series.ytTitleTemplate' => 'yt_title_template',
										'series.ytDescriptionTemplate' => 'yt_description_template',
										'series.levelSignificant' => 'level_significant'
										);
										
	protected static $db_join_fields = array();
	
	public $id;
	public $code;
	public $title;
	public $shot_at;
	public $checkable_at;
	public $series_trt;
	public $yt_title_template;
	public $yt_description_template;
	public $level_significant;

	public static function get_series_title_from_id($series_id) {
		$series = Series::find_by_id($series_id);
		return $series->title;
	}
}
?>