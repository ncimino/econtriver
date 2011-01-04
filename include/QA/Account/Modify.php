<?php
class QA_Account_Modify {
	
	const NEW_ACCT_STATE = 1;
	
	static function add($acctName,$db) {
		$sql = "INSERT INTO ".QA_DB_Table::ACCT." (name,active)
				VALUES ('".Normalize::mysql($acctName)."',
						".self::NEW_ACCT_STATE.");";
		return $db->query($sql);
	}

	static function addOwner($acctId,$userId,$db) {
		$sql = "INSERT INTO ".QA_DB_Table::OWNERS." (acct_id,owner_id)
				VALUES (".Normalize::mysql($acctId).",
						".Normalize::mysql($userId).");";
		return $db->query($sql);
	}

	static function state($active,$acctId,$userId,$db) {
		$sql = "UPDATE ".QA_DB_Table::ACCT.",".QA_DB_Table::OWNERS." 
				  SET active = ".Normalize::mysql($active)."
				WHERE ".QA_DB_Table::ACCT.".id = ".Normalize::mysql($acctId)."
				  AND acct_id = ".QA_DB_Table::ACCT.".id 
				  AND owner_id = ".Normalize::mysql($userId).";";
		return $db->query($sql);
	}

	static function update($name,$acctId,$userId,$db) {
		$sql = "UPDATE ".QA_DB_Table::ACCT.",".QA_DB_Table::OWNERS."
				  SET name = '".Normalize::mysql($name)."' 
				WHERE ".QA_DB_Table::ACCT.".id = ".Normalize::mysql($acctId)." 
				  AND acct_id = ".QA_DB_Table::ACCT.".id 
				  AND owner_id = ".Normalize::mysql($userId).";";
		return $db->query($sql);
	}
}
?>