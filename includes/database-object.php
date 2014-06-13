<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(LIB_PATH.DS.'database.php');

class DatabaseObject {
	
	public static function find_all() {
		return static::find_by_sql("SELECT * FROM ".static::$table_name);
	}
	
	public static function find_by_id($id=0) {
		$sql  = "SELECT ";		
		$i = 0;
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
		$sql .= "LIMIT 1 ";
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function find_all_limit($limit) {
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
		if($limit) {
			$sql .= "LIMIT {$limit} ";
		}
		return self::find_by_sql($sql);
	}
	
	public static function find_by_sql($sql="") {
		global $database;
		$result_set = $database->query($sql);
		$object_array = array();
		while ($row = $database->fetch_array($result_set)) {
			$object_array[] = static::instantiate($row);
		}
		return $object_array;
	}

	public static function count_all() {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".static::$table_name;
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}

	private static function instantiate($record) {
		$object = new static;
		
		foreach($record as $attribute=>$value){
			if($object->has_attribute($attribute)) {
				$object->$attribute = $value;
			}
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
		// We don't care about the value, we just want to know if the key exists
		// Will return true or false
		return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
		// return an array of attribute names and their values
		// There's a problem with this function
		$attributes = array();
		foreach(static::$db_view_fields as $field) {
		    if(property_exists($this, $field)) {
		      $attributes[$field] = $this->$field;
		    }
		}
		return $attributes;
	}
		
	protected function sanitized_attributes() {
		global $database;
		$clean_attributes = array();
		// sanitize the values before submitting
		// Note: does not alter the actual value of each attribute
		foreach($this->attributes() as $key => $value){
			$clean_attributes[$key] = $value;
		}
		return $clean_attributes;
	}
	
	public function save() {
		// A new record won't have an id yet.
		return isset($static->id) ? $static->update() : $static->create();
	}
	
	public static function find_all_child_for_parent($parent_id, $child_table_name, $parent_table_name, $group_by_sql) {
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
		$sql .= "WHERE ".$child_table_name.".fk".ucwords($parent_table_name)." = ".$parent_id." ";
		$sql .= $group_by_sql;
		return self::find_by_sql($sql);
	}
	
	public function delete() {
		global $database;
		// - DELETE FROM table WHERE condition LIMIT 1
		$sql = "DELETE FROM ".static::$table_name;
		$sql .= " WHERE id=". $database->escape_value($this->id);
		$sql .= " LIMIT 1";
		$database->query($sql);
		$action_target = static::$table_name . " - " . $this->id;
		log_action($action_target, "deleted");
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	public function update() {
		global $database;
		$attribute_pairs = array();
		foreach(static::$db_edit_fields as $key=>$value) {
		    if(property_exists($this, $value)) {
		      $attribute_pairs[] = "{$key}='{$database->escape_value($this->$value)}'";
		    }
		}
		$sql = "UPDATE ".static::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=". $database->escape_value($this->id);
		$database->query($sql);
		$action_target = static::$table_name . " - " . $this->id;
		log_action($action_target, "updated");
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	public function create() {
		global $database;
		$attributes = static::$db_edit_fields;
		$values = array();
		foreach (array_values($attributes) as $value) {
			$values[] = $this->$value;
		}
		$sql = "INSERT INTO ".static::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", $values);
		$sql .= "')";
		$action_target = static::$table_name;
		log_action($action_target, "created record");
		if($database->query($sql)) {
	    	$this->id = $database->insert_id();
	    	return true;
	    } else {
	    	return false;
	    }
	}
}





