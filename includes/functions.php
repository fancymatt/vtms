<?php

// New Functions File -- OO Version

$errors = array();

function fieldname_as_text($fieldname) {
	$fieldname = str_replace("_", " ", $fieldname);
	$fieldname = ucfirst($fieldname);
	return $fieldname;
}

function has_presence($value) {
	return isset($value) && $value !== "";
}

function validate_presences($required_fields) {
	global $errors;
	foreach($required_fields as $field) {
		$value = trim($_POST[$field]);
		if (!has_presence($value)) {
			$errors[$field] = fieldname_as_text($field) . " can't be blank";
		}
	}
	$_SESSION["errors"] = $errors;
}

function validate_max_lengths($fields_with_max_lengths) {
	global $errors;
	
}

function strip_zeros_from_date( $marked_string="" ) {
  // first remove the marked zeros
  $no_zeros = str_replace('*0', '', $marked_string);
  // then remove any remaining marks
  $cleaned_string = str_replace('*', '', $no_zeros);
  return $cleaned_string;
}

function redirect_to( $location = NULL ) {
  if ($location != NULL) {
    header("Location: {$location}");
    exit;
  }
}

function output_message($message="") {
  if (!empty($message)) { 
    return "<p class=\"message\">{$message}</p>";
  } else {
    return "";
  }
}

function __autoload($class_name) {
	$class_name = strtolower($class_name);
  $path = LIB_PATH.DS."{$class_name}.php";
  if(file_exists($path)) {
    require_once($path);
  } else {
		die("The file {$class_name}.php could not be found.");
	}
}

function include_layout_template($template="") {
	global $page_title;
	include(SITE_ROOT.DS.'public'.DS.'layouts'.DS.$template);
}

function log_action($action, $message="") {
	$logfile = SITE_ROOT.DS.'logs'.DS.'log.txt';
	$new = file_exists($logfile) ? false : true;
	$current_user = User::find_by_id($_SESSION['user_id']);
  if($handle = fopen($logfile, 'a')) { // append
    $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
		$content = "{$timestamp} | {$action}: {$message} - {$current_user->user_name}\n";
    fwrite($handle, $content);
    fclose($handle);
    if($new) { chmod($logfile, 0755); }
  } else {
    echo "Could not open log file for writing.";
  }
}

function datetime_to_text($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

function seconds_to_timecode($seconds="",$digits=4) {
	// todo: 
	// handling for max digits
	// 3 = 1:21
	// 4 = 01:21
	// 5 = 0:01:21
	// 6 = 00:01:21
	if($digits==6) {
		$hours = floor($seconds / 3600);

		return $hours.":".gmdate("i:s", $seconds);
	} else {
		return gmdate("i:s", $seconds);
	}

}

function logged_in() {
		return isset($_SESSION['user_id']);
	}
	
function confirm_logged_in() {
		if (!logged_in()) {
			redirect_to("login.php");
		}
	}

function get_random_inspirational_quote() {
  $quotes = Array("You are one click away from a day of video task delights...",
                  "Alexander the Great wept when he realized there were no more video tasks to conquer...",
                  "The tasks desire to envelope you in a warm embrace..."
                  );
  return $quotes[array_rand($quotes)];
}

?>