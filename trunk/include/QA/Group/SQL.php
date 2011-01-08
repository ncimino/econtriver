<?php 
class QA_Group_SQL extends QA_DB_Table {
	function memberIs($userId,$active=NULL) {
		$sql = self::groupAndUserGroup($active)." 
          AND ".self::USER_GROUPS.".user_id = ".Normalize::mysql($userId);
		return $sql;
	}
	
	function groupAndUserGroup($active=NULL) {
		$sql = self::GROUP.".id = ".self::USER_GROUPS.".group_id";
		$sql .= (isset($active)) ? " AND ".self::USER_GROUPS.".active = ".Normalize::mysql($active) : "";
		return $sql;
	}
}
?>