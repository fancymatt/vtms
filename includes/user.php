<?php
require_once(LIB_PATH.DS.'database.php');

class User extends DatabaseObject {
	protected static $table_name="user";
	protected static $db_view_fields = array('user.id' => 'id',
										'user.userName' => 'user_name',
										'user.password' => 'user_password',
										'user.fkPrivilegeType' => 'privilege_type',
										'user.fkTeamMember' => 'team_member_id',
										'user.timeZone' => 'time_zone',
										'teamMember.nameFirst' => 'member_name'
										);
										
	protected static $db_edit_fields = array('user.id' => 'id',
										'user.userName' => 'user_name',
										'user.password' => 'user_password',
										'user.fkPrivilegeType' => 'privilege_type',
										'user.fkTeamMember' => 'team_member_id',
										'user.timeZone' => 'time_zone'
										);
										
	protected static $db_join_fields = array('teamMember' => 'user.fkTeamMember=teamMember.id',
											 'privilegeType' => 'user.fkPrivilegeType=privilegeType.id'
											 );
	
	public $id;
	public $user_name;
	public $user_password;
	public $privilege_type;
	public $team_member_id;
	public $member_name;
	public $time_zone;
	
	public static function find_by_id($id=0) {
		$sql  = "SELECT ";		
		foreach (static::$db_view_fields as $k => $v) {
			$sql .= $k." AS ".$v;
			$i++;
			$i <= count(static::$db_view_fields) - 1 ? $sql .= ", " : $sql .= " ";
		}
		$sql .= "FROM ".static::$table_name." ";
		foreach (static::$db_join_fields as $k => $v) {
			$sql .= "LEFT JOIN ".$k." ON ".$v." ";
		}
		$sql .= "WHERE ".static::$table_name.".id = ".$id." ";
		$sql .= "LIMIT 1 ";
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function find_all() {
		return self::find_all_limit(0);
	}
		
	public static function attempt_login($username, $password) {
		$user = User::find_user_by_username($username);
		
		if ($user->member_name) {
			// now check password
			if ($user->password_check($password, $user->user_password)) {
				// password matches
				return $user;
			} else {
				return false;
			}
		} else {
			// user not found
			return false;
		}
	}
	
	public static function find_user_by_username($username) {
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
		$sql .= "WHERE user.userName = '".$username."' ";
		$sql .= "LIMIT 1 ";
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}

	protected function password_check($password, $existing_hash) {
		$hash = crypt($password, $existing_hash);
		if ($hash === $existing_hash) {
			return true;
		} else {
			return false;
		}
	}
	
	public function local_time($timestamp) {
	
		$time = new DateTime($timestamp, new DateTimeZone('UTC'));
		$time->setTimeZone(new DateTimeZone($this->time_zone));
		
		return $time->format('Y-m-d H:i:s');
	}

}
?>