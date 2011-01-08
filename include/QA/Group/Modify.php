<?php
class QA_Group_Modify {
	function add($grpName,$db) {
		$sql = "INSERT INTO ".QA_DB_Table::GROUP." (name)
				VALUES ('".Normalize::mysql($grpName)."');";
		return $db->query($sql);
	}

	function addUser($grpId,$userId,$db,$active=QA_DB_Table::ACTIVE) {
		$sql = "INSERT INTO ".QA_DB_Table::USER_GROUPS." (group_id,user_id,active)
				VALUES (".Normalize::mysql($grpId).",
						".Normalize::mysql($userId).",
						".Normalize::mysql($active).");";
		return $db->query($sql);
	}
	
	function state($active,$grpId,$userId,$db) {
		$sql = "UPDATE ".QA_DB_Table::USER_GROUPS." 
				   SET active = ".Normalize::mysql($active)." 
				 WHERE user_id = ".Normalize::mysql($userId)." AND 
					   group_id = ".Normalize::mysql($grpId).";";
		return $db->query($sql);
	}

	function dropUser($grpId,$userId,$db) {
		$sql = "DELETE FROM ".QA_DB_Table::USER_GROUPS." 
				 WHERE user_id = ".Normalize::mysql($userId)." AND 
				 	   group_id = ".Normalize::mysql($grpId).";";
		return $db->query($sql);
	}

	function removeIfEmpty($grpId,$db) {
		if (QA_Group_Select::isEmpty($grpId,$db)) {
			if (self::deleteLinks($grpId,$db) and self::delete($grpId,$db)) 
				return true;
			else
				throw new Exception("Failed to remove group or group relationship for group: ".QA_Group_Select::getGroupNameById($grpId,$db)." ({$grpId})");
		} else {
			return false;
		}
	}

	function delete($grpId,$db) {
		$sql = "DELETE FROM ".QA_DB_Table::GROUP." 
				 WHERE id = ".Normalize::mysql($grpId).";";
		return $db->query($sql);
	}

	function deleteLinks($grpId,$db) {
		$sql = "DELETE FROM ".QA_DB_Table::SHARE." 
				 WHERE group_id = ".Normalize::mysql($grpId).";";
		return $db->query($sql);
	}

	function update($name,$id,$db) {
		$sql = "UPDATE ".QA_DB_Table::GROUP." 
				   SET name = '".Normalize::mysql($name)."' 
				 WHERE id = ".Normalize::mysql($id).";";
		return $db->query($sql);
	}
	
}
?>