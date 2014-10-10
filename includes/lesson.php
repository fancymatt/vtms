<?php
require_once(LIB_PATH.DS.'database.php');

class Lesson extends DatabaseObject {
	protected static $table_name="lesson";
	protected static $db_view_fields = array('lesson.id' => 'id',
										'series.id' => 'series_id',
										'series.title' => 'series_name',
										'lesson.fkLanguageSeries' => 'language_series_id',
										'languageSeries.seriesTitle' => 'language_series_title',
										'language.id' => 'language_id',
										'language.name' => 'language_name',
										'lesson.number' => 'number',
										'IF ((SELECT SUM(taskGlobal.completionValue) FROM task JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE task.fkLesson=lesson.id AND task.isCompleted=1) >= (SELECT series.shotAt FROM series WHERE lesson.fkLanguageSeries=languageSeries.id AND languageSeries.fkSeries=series.id), 1, 0)' => 'is_shot',
										'IF ((SELECT SUM(taskGlobal.completionValue) FROM task JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE task.fkLesson=lesson.id AND task.isCompleted=1) >= (SELECT series.checkableAt FROM series WHERE lesson.fkLanguageSeries=languageSeries.id AND languageSeries.fkSeries=series.id), 1, 0)' => 'is_checkable',
										'(SELECT SUM(taskGlobal.completionValue) FROM task JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE task.fkLesson=lesson.id AND IF(taskGlobal.isAsset=1, task.isDelivered=1, task.isCompleted=1))' => 'comp_value',
										'lesson.title' => 'title',
										'lesson.trt' => 'trt',
										'lesson.checkedLanguage' => 'checked_language',
										'lesson.checkedVideo' => 'checked_video',
										'lesson.filesMoved' => 'files_moved',
										'level.code' => 'level_code',
										'lesson.publishDateSite' => 'publish_date',
										'lesson.publishDateSite - INTERVAL (lesson.bufferLength) DAY' => 'buffered_publish_date',
										'lesson.qa_log' => 'qa_log',
										'lesson.qa_url' => 'qa_url',
										'lesson.isQueued' => 'is_queued',
										'lesson.isDetected' => 'is_detected',
										'lesson.queuedTime' => 'queued_time',
										'lesson.exportedTime' => 'exported_time',
										'lesson.detectedTime' => 'detected_time',
										'lesson.timeUploadedDropbox' => 'dropbox_time',
										'(SELECT task.id FROM task WHERE task.fkLesson = lesson.id ORDER BY task.timeCompleted DESC LIMIT 1)' => 'last_task_id',
										'(SELECT MAX(task.timeCompleted) FROM task WHERE task.fkLesson = lesson.id)' => 'last_task_time',
										'(SELECT MAX(taskComment.timeCompleted) FROM taskComment JOIN task ON taskComment.fkTask=task.id WHERE task.fkLesson = lesson.id)' => 'last_issue_time',
										'(SELECT taskComment.id FROM taskComment JOIN task ON task.id = taskComment.fkTask WHERE task.fkLesson = lesson.id ORDER BY taskComment.timeCompleted DESC LIMIT 1)' => 'last_issue_id',
										'IF(IFNULL((SELECT MAX(task.timeCompleted) FROM task WHERE task.fkLesson = lesson.id),0) > IFNULL((SELECT MAX(taskComment.timeCompleted) FROM taskComment JOIN task ON taskComment.fkTask=task.id WHERE task.fkLesson = lesson.id),0), "task", "issue")' => 'last_action'
										);
										
	protected static $db_edit_fields = array('lesson.fkLanguageSeries' => 'language_series_id',
											'lesson.number' => 'number',
											'lesson.title' => 'title',
											'lesson.trt' => 'trt',
											'lesson.checkedLanguage' => 'checked_language',
											'lesson.checkedVideo' => 'checked_video',
											'lesson.filesMoved' => 'files_moved',
											'lesson.qa_log' => 'qa_log',
											'lesson.qa_url' => 'qa_url',
											'lesson.isQueued' => 'is_queued',
											'lesson.isDetected' => 'is_detected',
											'lesson.queuedTime' => 'queued_time',
											'lesson.exportedTime' => 'exported_time',
											'lesson.detectedTime' => 'detected_time',
											'lesson.publishDateSite' => 'publish_date',
											'lesson.timeUploadedDropbox' => 'dropbox_time'
											);
										
	protected static $db_join_fields = array('languageSeries' => 'languageSeries.id=lesson.fkLanguageSeries',
											 'language' => 'language.id=languageSeries.fkLanguage',
											 'series' => 'series.id=languageSeries.fkSeries',
											 //'task' => 'task.fkLesson=lesson.id',
											 //'taskComment' => 'taskComment.fkTask=task.id',
											 //'taskGlobal' => 'task.fkTaskGlobal=taskGlobal.id',
											 'level' => 'languageSeries.fkLevel=level.id'
											 );
	
	public $id;
	public $series_id;
	public $series_name;
	public $language_series_id;
	public $language_series_title;
	public $language_id;
	public $language_name;
	public $comp_value;
	public $number;
	public $title;
	public $trt;
	public $is_shot;
	public $is_checkable;
	public $checked_video;
	public $checked_language;
	public $is_detected;
	public $files_moved;
	public $date_due;
	public $level_code;
	public $issues_remaining;
	public $qa_log;
	public $qa_url;
	public $is_queued;
	public $queued_time;
	public $exported_time;
	public $publish_date;
	public $buffered_publish_date;
	public $time_dropbox;
	public $last_task_id;
	public $last_issue_id;
	public $last_task_time;
	public $last_issue_time;
	public $last_action;
	public $detected_time;
	
	public static function find_all_lessons_for_language_series($language_series_id) {
		$child_table_name = "lesson";
		$parent_table_name = "LanguageSeries";
		$group_by_sql = "GROUP BY lesson.id ORDER BY lesson.number ASC";
		return self::find_all_child_for_parent($language_series_id, $child_table_name, $parent_table_name, $group_by_sql);
	}
	
	public static function find_all_lessons_for_series($series_id) {
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
		$sql .= "WHERE languageSeries.fkSeries = " . $series_id ." ";
		return static::find_by_sql($sql);
	}
	
	public static function find_all_completed_lessons_for_language_series($language_series_id) {
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
		$sql .= "WHERE lesson.fkLanguageSeries = " . $language_series_id ." ";
		$sql .= "AND lesson.filesMoved=1 ";
		return static::find_by_sql($sql);
	}
	
	public static function find_all_upcoming_due_lessons($days_from_now=7) {
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
		$sql .= "WHERE NOT lesson.filesMoved = 1 ";
		$sql .= "AND DATE(lesson.publishDateSite) < CURDATE() + INTERVAL {$days_from_now} DAY ";
		$sql .= "AND DATE(lesson.publishDateSite) > 0 ";
		$sql .= "GROUP BY lesson.id ";
		$sql .= "ORDER BY publish_date ASC, series.title ASC, language.name ASC ";
		return static::find_by_sql($sql);
	}
	
	public static function find_all_lessons_publishing_on_date($date) {
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
		$sql .= "WHERE DATE(lesson.publishDateSite) = '{$date}' ";
		$sql .= "GROUP BY lesson.id ";
		$sql .= "ORDER BY publish_date ASC, series.title ASC, language.name ASC, lesson.number ASC ";
		return static::find_by_sql($sql);
	}
	
	public static function find_all_qa_lessons() {
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
		$sql .= "WHERE NOT lesson.checkedLanguage = 1 ";
		// is_checkable
		$sql .= "AND IF ((SELECT SUM(taskGlobal.completionValue) FROM task JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE task.fkLesson=lesson.id AND task.isCompleted=1) >= (SELECT series.checkableAt FROM series WHERE lesson.fkLanguageSeries=languageSeries.id AND languageSeries.fkSeries=series.id), 1, 0) = 1 ";
		$sql .= "AND LENGTH(lesson.qa_url) > 0 ";
		$sql .= "GROUP BY lesson.id ";
		$sql .= "ORDER BY language.name ASC, series.title ASC ";
		return static::find_by_sql($sql);
	}
	
	public function add_to_dropbox() {
		global $database;
		$current_time = new DateTime(null, new DateTimeZone('UTC'));
		
		// Then update
		$sql  = "UPDATE lesson ";
		$sql .= "SET timeUploadedDropbox='{$current_time->format('Y-m-d H:i:s')}' ";
		$sql .= "WHERE id={$this->id} ";
		$sql .= "LIMIT 1";
		
		$database->query($sql);
	}
	
	// Operations Page Functions
	
	public static function find_all_exportable_lessons() {
		// detect the latest task completion time and issue fixed time
		// last issue fixed, exported, yet it still appears. If last export time > last issue time, don't show
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
		$sql .= "JOIN task ON task.fkLesson=lesson.id ";
		$sql .= "LEFT JOIN taskComment ON taskComment.fkTask=task.id ";
		$sql .= "WHERE NOT lesson.filesMoved=1 ";
		$sql .= "AND NOT lesson.isQueued=1 ";
		$sql .= "GROUP BY lesson.id ";
		
		// All issues for this lesson have been fixed, or there were never any issues
		$sql .= "HAVING ((Count(taskComment.id) - Sum(taskComment.isCompleted) = 0) ";
		$sql .= "        OR Count(taskComment.id) < 1) ";
		
		// Current lesson completion value is greater than or equal to series' "checkable at" value 
		$sql .= "     AND ((SELECT Sum(taskGlobal.completionValue) ";
		$sql .= "          FROM   lesson sub_lesson ";
		$sql .= "                 JOIN task ";
		$sql .= "                   ON task.fkLesson = sub_lesson.id ";
		$sql .= "                 JOIN taskGlobal ";
		$sql .= "                   ON task.fkTaskGlobal = taskGlobal.id ";
		$sql .= "          WHERE  task.isCompleted = 1 ";
		$sql .= "                 AND sub_lesson.id = lesson.id) >= ";
		$sql .= "         (SELECT series.checkableAt ";
		$sql .= "           FROM  lesson sub_lesson ";
		$sql .= "                 JOIN languageSeries ";
		$sql .= "                   ON sub_lesson.fkLanguageSeries = languageSeries.id ";
		$sql .= "             	  JOIN series ";
		$sql .= "                   ON languageSeries.fkSeries = series.id ";
		$sql .= "			WHERE  lesson.id = sub_lesson.id)) ";
		
		// Last issue fixed time OR last task finished time is greater than last exported time
		$sql .= "	AND (( ";
		$sql .= "		lesson.exportedTime < ";
		$sql .= "			(SELECT MAX(task.timeCompleted) ";
		$sql .= "				FROM lesson sub_lesson ";
		$sql .= "					JOIN task ";
		$sql .= "					  ON sub_lesson.id=task.fkLesson ";
		$sql .= "				WHERE sub_lesson.id=lesson.id) ";
		$sql .= "  		) OR ( ";
		$sql .= "		lesson.exportedTime < ";
		$sql .= "			(SELECT MAX(taskComment.timeCompleted) ";
		$sql .= "				FROM lesson sub_lesson ";
		$sql .= "					JOIN task ";
		$sql .= "					  ON sub_lesson.id=task.fkLesson ";
		$sql .= "					JOIN taskComment ";
		$sql .= "					  ON task.id=taskComment.fkTask ";
		$sql .= "				WHERE sub_lesson.id=lesson.id) ";
		$sql .= "  		) OR ( ";
		$sql .= "		lesson.exportedTime < 1 )) ";
		//$sql .= "ORDER BY lesson.publishDateSite DASC ";
		
		return static::find_by_sql($sql);
	}

   	public static function find_all_queued_lessons() {
		// detect the latest task completion time and issue fixed time
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
		$sql .= "WHERE lesson.isQueued = 1 ";
		$sql .= "AND NOT lesson.filesMoved =1 ";
		$sql .= "ORDER BY lesson.queuedTime DESC ";
		
		return static::find_by_sql($sql);
	}
	
	public static function get_recently_detected_lessons() {
		// detect the latest task completion time and issue fixed time
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
		$sql .= "WHERE lesson.isDetected = 1 ";
		$sql .= "ORDER BY lesson.detectedTime DESC ";
		$sql .= "LIMIT 50 ";
		
		return static::find_by_sql($sql);
	}
	
	public static function find_all_ready_to_video_check_lessons($sort_by='abc') {
		// detect the latest task completion time and issue fixed time
		// and there are no more missing tasks
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
		$sql .= "JOIN task ON task.fkLesson=lesson.id ";
		$sql .= "LEFT JOIN taskComment ON taskComment.fkTask=task.id ";
		$sql .= "WHERE NOT lesson.checkedVideo = 1 ";
		$sql .= "AND lesson.checkedLanguage = 1 ";
		$sql .= "AND NOT lesson.filesMoved = 1 ";
		$sql .= "GROUP BY lesson.id ";
		// All issues for this lesson have been fixed, or there were never any issues
		$sql .= "HAVING ((Count(taskComment.id) - Sum(taskComment.isCompleted) = 0) ";
		$sql .= "        OR Count(taskComment.id) < 1) ";
		
		// Last issue fixed time OR last task finished time is less than last exported time
		$sql .= "	AND (lesson.exportedTime > ";
		$sql .= "			(SELECT MAX(task.timeCompleted) ";
		$sql .= "				FROM lesson sub_lesson ";
		$sql .= "					JOIN task ";
		$sql .= "					  ON sub_lesson.id=task.fkLesson ";
		$sql .= "				WHERE sub_lesson.id=lesson.id)) ";
		$sql .= "	AND	(lesson.exportedTime > ";
		$sql .= "			(SELECT IF( MAX( taskComment.timeCompleted ) IS NULL , 0, MAX( taskComment.timeCompleted ) ) ";
		$sql .= "				FROM lesson sub_lesson ";
		$sql .= "					JOIN task ";
		$sql .= "					  ON sub_lesson.id=task.fkLesson ";
		$sql .= "					JOIN taskComment ";
		$sql .= "					  ON task.id=taskComment.fkTask ";
		$sql .= "				WHERE sub_lesson.id=lesson.id) ";
		$sql .= "  		) ";
		
		// No missing tasks
		$sql .= "	AND (SELECT COUNT(task.id) ";
		$sql .= "		FROM lesson sub_lesson ";
		$sql .= "			JOIN task ";
		$sql .= "				 ON sub_lesson.id=task.fkLesson ";
		$sql .= "		WHERE sub_lesson.id=lesson.id ";
		$sql .= "		  AND task.isCompleted=1 ) = ";
		$sql .= "		(SELECT COUNT(task.id) ";
		$sql .= "		FROM lesson sub_lesson ";
		$sql .= "			JOIN task ";
		$sql .= "				 ON sub_lesson.id=task.fkLesson ";
		$sql .= "		WHERE sub_lesson.id=lesson.id) ";
		if ($sort_by=='abc') {
			$sql .= "ORDER BY language.name, series.title, level.id, lesson.number ASC ";
		} else if ($sort_by=='pub') {
			$sql .= "ORDER BY lesson.publishDateSite ASC ";	
		} else {
			$sql .= "ORDER BY lesson.exportedTime DESC ";
		}
		
		return static::find_by_sql($sql);
	}
	
	public static function find_all_checkable_lessons($sort_by='abc') {
		// Now doing double duty as the method that checks for operations and qa page
		// When there are no issues on a lesson, it is not appearing
		
		// $findQALessons->addFindCriterion('Completion Value Total', '>19');
		// $findQALessons->addFindCriterion('Exported Last Time', '*');
		// $findQALessons->addFindCriterion('QA URL', '*');
		// $findQALessons->addSortRule('Language::Language Name', 1, FILEMAKER_SORT_ASCEND);

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
		$sql .= "LEFT JOIN task ON task.fkLesson=lesson.id ";
		$sql .= "LEFT JOIN taskComment ON taskComment.fkTask=task.id ";
		$sql .= "WHERE NOT lesson.checkedLanguage = 1 ";
		$sql .= "AND NOT lesson.filesMoved=1 ";
		//$sql .= "AND NOT lesson.qa_url='' ";
		$sql .= "AND lesson.exportedTime > 0 ";
		$sql .= "AND IF ((SELECT SUM(taskGlobal.completionValue) FROM task JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE task.fkLesson=lesson.id AND task.isCompleted=1) >= (SELECT series.checkableAt FROM series WHERE lesson.fkLanguageSeries=languageSeries.id AND languageSeries.fkSeries=series.id), 1, 0) = 1 ";
		$sql .= "GROUP BY lesson.id ";
		$sql .= "HAVING (Count(taskComment.id) - Sum(taskComment.isCompleted) = 0 ";
		$sql .= "        OR Count(taskComment.id) < 1) ";
		$sql .= "		AND lesson.exportedTime > ";
		$sql .= "			(SELECT MAX(task.timeCompleted) ";
		$sql .= "				FROM lesson sub_lesson ";
		$sql .= "					JOIN task ";
		$sql .= "					  ON sub_lesson.id=task.fkLesson ";
		$sql .= "				WHERE sub_lesson.id=lesson.id) ";
		
		// Exported after last issue
		$sql .= "		AND (lesson.exportedTime > ";
		$sql .= "			(SELECT IF( MAX( taskComment.timeCompleted ) IS NULL , 0, MAX( taskComment.timeCompleted ) ) ";
		$sql .= "				FROM lesson sub_lesson ";
		$sql .= "					JOIN task ";
		$sql .= "					  ON sub_lesson.id=task.fkLesson ";
		$sql .= "					LEFT JOIN taskComment ";
		$sql .= "					  ON task.id=taskComment.fkTask ";
		$sql .= "				WHERE sub_lesson.id=lesson.id)) ";
		
		// Past Checkable Completion Value
		$sql .= "       AND (SELECT Sum(taskGlobal.completionValue) ";
		$sql .= "            FROM   lesson sub_lesson ";
		$sql .= "                   JOIN task ";
		$sql .= "                     ON task.fkLesson = sub_lesson.id ";
		$sql .= "                   JOIN taskGlobal ";
		$sql .= "                     ON task.fkTaskGlobal = taskGlobal.id ";
		$sql .= "                   JOIN languageSeries ";
		$sql .= "                     ON sub_lesson.fkLanguageSeries = languageSeries.id ";
		$sql .= "                   JOIN series ";
		$sql .= "                     ON languageSeries.fkSeries = series.id ";
		$sql .= "            WHERE  task.isCompleted = 1 ";
		$sql .= "                    AND sub_lesson.id = lesson.id) >= ";
		$sql .= "            (SELECT series.checkableAt ";
		$sql .= "             FROM   lesson sub_lesson_series ";
		$sql .= "                    JOIN languageSeries ";
		$sql .= "                      ON ";
		$sql .= "        sub_lesson_series.fkLanguageSeries = languageSeries.id ";
		$sql .= "                JOIN series ";
		$sql .= "                  ON ";
		$sql .= "        languageSeries.fkSeries = series.id ";
		$sql .= "         WHERE  lesson.id = sub_lesson_series.id ";
		$sql .= "			) ";
		if ($sort_by=='abc') {
			$sql .= "ORDER BY language.name, series.title, level.id, lesson.number ASC ";
		} else if ($sort_by=='pub') {
			$sql .= "ORDER BY lesson.publishDateSite ASC ";	
		} else {
			$sql .= "ORDER BY lesson.exportedTime DESC ";
		}
		return static::find_by_sql($sql);
	}
	
	public static function find_qa_lessons($sort_by='abc') {
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
		$sql .= "LEFT JOIN task ON task.fkLesson=lesson.id ";
		$sql .= "LEFT JOIN taskComment ON taskComment.fkTask=task.id ";
		$sql .= "WHERE NOT lesson.checkedLanguage = 1 ";
		$sql .= "AND NOT lesson.filesMoved=1 ";
		$sql .= "AND NOT lesson.qa_url='' ";
		$sql .= "AND lesson.exportedTime > 0 ";
		$sql .= "AND NOT language.id=9 ";
		$sql .= "AND NOT language.id=39 ";
		//$sql .= "AND IF ((SELECT SUM(taskGlobal.completionValue) FROM task JOIN taskGlobal ON task.fkTaskGlobal=taskGlobal.id WHERE task.fkLesson=lesson.id AND task.isCompleted=1) >= (SELECT series.checkableAt FROM series WHERE lesson.fkLanguageSeries=languageSeries.id AND languageSeries.fkSeries=series.id), 1, 0) = 1 ";
		$sql .= "GROUP BY lesson.id ";
		//$sql .= "HAVING (Count(taskComment.id) - Sum(taskComment.isCompleted) = 0 ";
		//$sql .= "        OR Count(taskComment.id) < 1) ";
		$sql .= "	HAVING lesson.exportedTime > ";
		$sql .= "			(SELECT MAX(task.timeCompleted) ";
		$sql .= "				FROM lesson sub_lesson ";
		$sql .= "					JOIN task ";
		$sql .= "					  ON sub_lesson.id=task.fkLesson ";
		$sql .= "				WHERE sub_lesson.id=lesson.id) ";
		
		// Past Checkable Completion Value
		$sql .= "       AND (SELECT Sum(taskGlobal.completionValue) ";
		$sql .= "            FROM   lesson sub_lesson ";
		$sql .= "                   JOIN task ";
		$sql .= "                     ON task.fkLesson = sub_lesson.id ";
		$sql .= "                   JOIN taskGlobal ";
		$sql .= "                     ON task.fkTaskGlobal = taskGlobal.id ";
		$sql .= "                   JOIN languageSeries ";
		$sql .= "                     ON sub_lesson.fkLanguageSeries = languageSeries.id ";
		$sql .= "                   JOIN series ";
		$sql .= "                     ON languageSeries.fkSeries = series.id ";
		$sql .= "            WHERE  task.isCompleted = 1 ";
		$sql .= "                    AND sub_lesson.id = lesson.id) >= ";
		$sql .= "            (SELECT series.checkableAt ";
		$sql .= "             FROM   lesson sub_lesson_series ";
		$sql .= "                    JOIN languageSeries ";
		$sql .= "                      ON ";
		$sql .= "        sub_lesson_series.fkLanguageSeries = languageSeries.id ";
		$sql .= "                JOIN series ";
		$sql .= "                  ON ";
		$sql .= "        languageSeries.fkSeries = series.id ";
		$sql .= "         WHERE  lesson.id = sub_lesson_series.id ";
		$sql .= "			) ";
		if($sort_by == 'pub') {
			$sql .= "ORDER BY lesson.publishDateSite ASC ";
		} else {
			$sql .= "ORDER BY language.name, series.title, level.id, lesson.number ASC ";
		}
		
		return static::find_by_sql($sql);
	}
	
	public static function find_all() {
		return self::find_all_limit(0);
	}
	
	public static function find_all_moveable_lessons() {
		// detect the latest task completion time and issue fixed time
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
		$sql .= "WHERE lesson.checkedVideo = 1 ";
		$sql .= "AND lesson.checkedLanguage = 1 ";
		$sql .= "AND NOT lesson.filesMoved = 1 ";
		$sql .= "ORDER BY lesson.publishDateSite ASC ";
		
		return static::find_by_sql($sql);
	}
	
	public static function find_all_recently_completed_lessons() {
		// detect the latest task completion time and issue fixed time
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
		$sql .= "WHERE lesson.filesMoved = 1 ";
		$sql .= "AND lesson.trt < 1 ";
		$sql .= "ORDER BY lesson.publishDateSite ASC ";
		
		return static::find_by_sql($sql);
	}
	
	public function display_full_lesson() {
		echo $this->language_name . " - " . $this->series_name . " (" . $this->level_code . ") #" . $this->number;
	}
	
	public function display_list_of_issues_with_link() {
		$issues = Issue::get_unfinished_issues_for_lesson($this->id);
		echo "<a href='issues-for-lesson.php?id=".$this->id."'>Issues: ".count($issues)."</a>";
	}
	
	public function display_full_lesson_navigation() {
		echo "<a href='series.php?id=".$this->series_id."'>";
		echo $this->series_name;
		echo "</a>";
		echo " > ";
		echo "<img src='images/{$this->level_code}.png'> ";
		echo "<a href='language-series.php?series=".$this->series_id."&id=".$this->language_series_id."'>";
		echo $this->language_series_title;
		echo "</a>";
		echo " > ";
		echo "#{$this->number} {$this->title}";
	}
	
	public function display_lesson_topbar($active_page="main") {
		
		if($active_page=="main") {
			echo "Lesson";
		} else {
			echo "<a href='lesson.php?series={$this->series_id}&langSeries={$this->language_series_id}&lesson={$this->id}'>Lesson</a>";
		}
		echo " | "; 
		if ($active_page=="script") {
			echo "Script";
		} else {
			echo "<a href='lesson-script.php?id={$this->id}'>Script</a>";
		}
	}
	
	public function display_lesson_status_bar() {
	  echo "<div class='lesson-production'>";
	  echo "  <div class='lesson-issues'>";
  	$issues = Issue::get_unfinished_issues_for_lesson($this->id);
  	echo "    <a class='issues-bar' href='#'>Issues: ".count($issues)."</a>";
  	echo "  </div>";
	  echo "  <div class='lesson-status'>";
    echo "	  <p class='lesson-status-item'>";
  	echo "      <img src='";
  	echo $this->is_shot ? 'img/lesson-status-yes-shot.png' : 'img/lesson-status-not-shot.png';
  	echo "'>";
  	echo "    </p>";
  	echo "    <p class='lesson-status-item'>";
  	echo "      <img src='";
  	echo $this->is_checkable ? 'img/lesson-status-yes-checkable.png' : 'img/lesson-status-not-checkable.png';
  	echo "'>";
  	echo "    </p>";
  	echo "	  <p class='lesson-status-item'>";
  	echo "      <img src='";
  	echo $this->checked_language ? 'img/lesson-status-yes-language.png' : 'img/lesson-status-not-language.png';
  	echo "'>";
  	echo "    </p>";
  	echo "	  <p class='lesson-status-item'>";
  	echo "      <img src='";
  	echo $this->checked_video ? 'img/lesson-status-yes-video.png' : 'img/lesson-status-not-video.png';
  	echo "'>";
  	echo "    </p>";
  	echo "	  <p class='lesson-status-item'>";
  	echo "      <img src='";
  	echo $this->files_moved ? 'img/lesson-status-yes-moved.png' : 'img/lesson-status-not-moved.png';
  	echo "'>";
  	echo "    </p>";
	  echo "  </div>";
	  echo "</div>";
	}
	
}
?>