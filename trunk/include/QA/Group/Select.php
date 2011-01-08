<?php
class QA_Group_Select extends QA_Group_SQL {
	function isEmpty($grpId,$db) {
		return (self::numOfMembers($grpId,$db) == 0);
	}
	
	function numOfMembers($grpId,$db) {
		$sql = "SELECT id 
				FROM ".self::USER_GROUPS." 
				WHERE ".self::USER_GROUPS.".group_id = ".Normalize::mysql($grpId).";";
		$db->query($sql);
		return $db->num();
	}
	
	function byMember($userId,$db,$active=NULL) {
		$sql = "SELECT * FROM ".self::GROUP.",".self::USER_GROUPS."
        		WHERE ".self::memberIs($userId,$active);
		return $db->query($sql);
	}
	
	function getGroupNameById($id,$db) {
		$sql = "SELECT name FROM ".QA_DB_Table::GROUP."
        WHERE ".QA_DB_Table::GROUP.".id = ".Normalize::mysql($id).";";
		$db->query($sql);
		$return = $db->fetch();
		return $return['name'];
	}

	function getGroupByName($name,$db) {
		$sql = "SELECT * FROM ".QA_DB_Table::GROUP."
        WHERE ".QA_DB_Table::GROUP.".name = '".Normalize::mysql($name)."';";
		$db->query($sql);
		return $db->fetch();
	}
	
}
?>