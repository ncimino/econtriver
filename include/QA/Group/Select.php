<?php
class QA_Group_Select {
	function isEmpty($grpId,$db) {
		return (self::numOfMembers($grpId,$db) == 0);
	}
	
	function numOfMembers($grpId,$db) {
		$sql = "SELECT id 
				  FROM ".QA_DB_Table::USER_GROUPS." 
				 WHERE grpId = ".Normalize::mysql($grpId).";";
		$db->query($sql);
		return $db->num();
	}
	
	function getActiveGroups($active,$userId,$db) {
		$sql = "SELECT * FROM ".QA_DB_Table::GROUP.",".QA_DB_Table::USER_GROUPS."
        WHERE ".QA_DB_Table::GROUP.".id = group_id 
          AND user_id = ".Normalize::mysql($userId)."
          AND active = ".Normalize::mysql($active).";";
		$this->activeGroups = $db->query($sql);
	}

	function getInactiveGroups() {
		$sql = "SELECT * FROM ".QA_DB_Table::GROUP.",".QA_DB_Table::USER_GROUPS."
        WHERE ".QA_DB_Table::GROUP.".id = group_id 
          AND user_id = {$this->user->getUserId()}
          AND active = 0;";
		$this->inactiveGroups = $this->DB->query($sql);
	}
}
?>