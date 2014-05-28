<?php
require_once(LIB_PATH.DS.'database.php');

class PrivilegeType extends DatabaseObject {
	protected static $table_name="privilegeType";
	protected static $db_view_fields = array('privilegeType.id' => 'id',
										'privilegeType.privilege' => 'privilege'
										);
										
	protected static $db_join_fields = array('privilegeType.id' => 'id',
										'privilegeType.privilege' => 'privilege'
										);

	public $id;
	public $privilege;
}
?>