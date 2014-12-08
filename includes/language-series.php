<?php
require_once(LIB_PATH.DS.'database.php');

class LanguageSeries extends DatabaseObject {
	protected static $table_name="languageSeries";
	
	protected static $db_edit_fields = array('seriesTitle' => 'language_series_title',
										'fkSeries' => 'series_id',
										'fkLanguage' => 'language_id',
										'fkLevel' => 'level_id',
										'fkChannel' => 'channel_id',
										'fkTalent' => 'talent_id',
										'onIllTv' => 'on_ill_tv'
										);
	
	protected static $db_view_fields = array('languageSeries.id' => 'id', 
										'languageSeries.seriesTitle' => 'language_series_title',
										'language.id' => 'language_id', 
										'language.name' => 'language_name', 
										'level.id' => 'level_id', 
										'level.name' => 'level_name', 
										'level.code' => 'level_code',
										'languageSeries.fkTalent' => 'talent_id',
										'series.id' => 'series_id', 
										'languageSeries.fkChannel' => 'channel_id',
										'series.title' => 'series_name',
										'SEC_TO_TIME((SELECT SUM(lesson.trt) FROM lesson WHERE lesson.fkLanguageSeries=languageSeries.id))' => 'total_trt',
										'onIllTv' => 'on_ill_tv'
										);
	protected static $db_join_fields=array("language" => "language.id=languageSeries.fkLanguage", 
											"series" => "series.id=languageSeries.fkSeries", 
											"level" => "level.id=languageSeries.fkLevel"
											);
	public $id;
	public $language_id;
	public $language_series_title;
	public $language_name;
	public $talent_id;
	public $level_id;
	public $channel_id;
	public $level_name;
	public $level_code;
	public $series_id;
	public $series_name;
	public $total_trt;
	public $on_ill_tv;
	
	public static function find_all_language_series_for_series($series_id) {
		$child_table_name = "languageSeries";
		$parent_table_name = "series";
		$group_by_sql = "GROUP BY id ORDER BY language.name ASC, level.id ASC";
		return self::find_all_child_for_parent($series_id, $child_table_name, $parent_table_name, $group_by_sql);
	}
	
	public static function find_all_language_series_for_language($language_id) {
		$child_table_name = "languageSeries";
		$parent_table_name = "language";
		$group_by_sql = "GROUP BY id ORDER BY series.title ASC, level.id ASC";
		return self::find_all_child_for_parent($language_id, $child_table_name, $parent_table_name, $group_by_sql);
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
	
	public function display_full_language_series_navigation() {
		echo "<a href='series.php?id=".$this->series_id."'>";
		echo $this->series_name;
		echo "</a>";
		echo " > ";
		echo "<img src='images/{$this->level_code}.png'> ";
		echo "<a href='language-series.php?series=".$this->series_id."&id=".$this->id."'>";
		echo $this->language_series_title;
		echo "</a>";
	}
	
	public function generate_ill_tv_code() {
  	
  	$language = Language::find_by_id($this->language_id);
  	$language_name = strtolower($language->name);
  	$url = $language->site_url_short;
  		
  	$output  = "";
  	$output .= "<rss xmlns:media=\"http://search.yahoo.com/mrss/\" xmlns:creativeCommons=\"http://backend.userland.com/creativeCommonsRssModule\" version=\"2.0\">\n";
  	$output .= "<channel>\n";
  	$output .= "<title>{$this->language_series_title}</title>\n";
  	$output .= "<link/>\n";
  	$output .= "<description></description>\n";
  	
  	$lessons = Lesson::find_all_ready_for_ill_tv_lessons_for_langauge_series($this->id);
  	foreach($lessons as $lesson) {
    	
    	$code = $lesson->lesson_code();
    	
    	$output .= "<item>\n";
    	$output .= "<title>{$lesson->title}</title>\n";
    	$output .= "<guid isPermaLink=\"false\">";
    	$output .= $code;
    	$output .= "</guid>\n";
    	$output .= "<description></description>\n";
    	$output .= "<media:group>\n";
    	$output .= "<media:content url=\"http://media.libsyn.com/media/{$url}/{$code}-h.mp4\" bitrate=\"1200\" ";
    	$output .= "duration=\"{$lesson->trt}\" medium=\"video\" type=\"video/quicktime\"/>\n";
    	$output .= "<media:content url=\"http://media.libsyn.com/media/{$url}/{$code}-m.mp4\" bitrate=\"800\" ";
    	$output .= "duration=\"{$lesson->trt}\" medium=\"video\" type=\"video/quicktime\"/>\n";
    	$output .= "<media:content url=\"http://media.libsyn.com/media/{$url}/{$code}-l.mp4\" bitrate=\"500\" ";
    	$output .= "duration=\"{$lesson->trt}\" medium=\"video\" type=\"video/quicktime\"/>\n";
    	$output .= "</media:group>\n";
    	$output .= "<media:thumbnail url=\"http://assets.languagepod101.com/roku/images/thumbs/{$language_name}/{$code}-thumb.png\"/>\n";
    	$output .= "</item>\n";
  	}
    $output .= "</channel>\n";
    $output .= "</rss>";
  	
  	return $output;

	}
}
?>