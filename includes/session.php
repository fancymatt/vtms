<?php
require_once(LIB_PATH.DS."config.php");

class Session {
	private $logged_in=false;
	public $user_id;
	public $privilege_type;
	public $message;

	function __construct() {
		session_start();
		$this->check_message();
		$this->check_login();
	    if($this->logged_in) {
	      // actions to take right away if user is logged in
	    } else {
	      // actions to take right away if user is not logged in
	    }
	}
	
	private function check_message() {
		// Is there a message stored in the session?
		if(isset($_SESSION['message'])) {
			// Add it as an attribute and erase the stored version
			$this->message = $_SESSION['message'];
			unset($_SESSION['message']);
		} else {
			$this->message = "";
		}
	}
	
	public function errors() {
		if (isset($_SESSION["errors"])) {
			$errors = $_SESSION["errors"];
			$_SESSION["errors"] = null;
			return $errors;
		}
	}
	
	public function form_errors($errors=array()) {
		$output = "";
		if (!empty($errors)) {
			$output .= "<div class=\"error\">";
			$output .= "Please fix the following errors:";
			$output .= "<ul>";
			foreach ($errors as $key => $error) {
				$output .= "<li>{$error}</li>";
			}
			$output .= "</ul>";
			$output .= "</div>";
			}
		return $output;
	}
	
	public function is_logged_in() {
		return $this->logged_in;
	}

	public function confirm_logged_in() {
		if (!logged_in()) {
			redirect_to("login.php");
		}	
	}
	
	public function login($user) {
		// database should find user based on username/password
		if($user){
			$this->user_id = $_SESSION['user_id'] = $user->id;
			$this->user_name = $_SESSION['user_name'] = $user->user_name;
			$this->privilege_type = $_SESSION['privilege_type'] = $user->privilege_type;
			
			$this->logged_in = true;
		}
	}
	
	public function logout() {
		unset($_SESSION['user_id']);
		unset($this->user_id);
		unset($_SESSION['user_name']);
		unset($this->user_name);
		unset($_SESSION['privilege_type']);
		unset($this->privilege_type);
		
		$this->logged_in = false;
	}

	public function message($msg="") {
		if(!empty($msg)) {
		    // then this is "set message"
		    // make sure you understand why $this->message=$msg wouldn't work
		    $_SESSION['message'] = $msg;
		} else {
		   // then this is "get message"
		   return $this->message;
		}
	}

	private function check_login() {
		if(isset($_SESSION['user_id'])) {
			$this->user_id = $_SESSION['user_id'];
			$this->logged_in = true;
		} else {
			unset($this->user_id);
			$this->logged_in = false;
		}
	}
	
	public function is_admin() {
		if($_SESSION['privilege_type'] == 1) {
			return true;
		} else {
			return false;
		}
	}
}
$session = new Session();
$message = $session->message();
?>