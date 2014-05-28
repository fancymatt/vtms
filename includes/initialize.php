<?php

// Define the core paths
// Define them as absolute paths to make sure that require_once works as expected

// DIRECTORY_SEPARATOR is a PHP pre-defined constant
// (\ for Windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

defined('SITE_ROOT') ? null : 
	define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'].DS.'vtms');

defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.DS.'includes');

// load config file first
require_once(LIB_PATH.DS.'config.php');

// load basic functions next so that everything after can use them
require_once(LIB_PATH.DS.'functions.php');

// load core objects
require_once(LIB_PATH.DS.'session.php');
require_once(LIB_PATH.DS.'database.php');
require_once(LIB_PATH.DS.'database_object.php');

// load database-related classes
require_once(LIB_PATH.DS.'lesson.php');
require_once(LIB_PATH.DS.'language.php');
require_once(LIB_PATH.DS.'language_series.php');
require_once(LIB_PATH.DS.'series.php');
require_once(LIB_PATH.DS.'shot.php');
require_once(LIB_PATH.DS.'task.php');
require_once(LIB_PATH.DS.'issue.php');
require_once(LIB_PATH.DS.'global_task.php');
require_once(LIB_PATH.DS.'global_task_statistic.php');
require_once(LIB_PATH.DS.'linked_image.php');
require_once(LIB_PATH.DS.'level.php');
require_once(LIB_PATH.DS.'privilege_type.php');
?>