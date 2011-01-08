<?php
class QA_Account_Modify extends QA_Account_SQL {
	
	const NEW_ACCT_STATE = 1;
	
	static function add($acctName,$db) {
		$sql = "INSERT INTO ".self::ACCT." (name,active)
				VALUES ('".Normalize::mysql($acctName)."',
						".self::NEW_ACCT_STATE.");";
		return $db->query($sql);
	}

	static function addOwner($acctId,$userId,$db) {
		$sql = "INSERT INTO ".self::OWNERS." (acct_id,owner_id)
				VALUES (".Normalize::mysql($acctId).",
						".Normalize::mysql($userId).");";
		return $db->query($sql);
	}

	static function state($active,$acctId,$userId,$db) {
		$sql = "UPDATE ".self::ACCT.",".self::OWNERS." 
				  SET active = ".Normalize::mysql($active)."
				WHERE ".self::acctAndOwnedBy($acctId,$userId).";";
		return $db->query($sql);
	}

	static function update($name,$acctId,$userId,$db) {
		$sql = "UPDATE ".self::ACCT.",".self::OWNERS."
				  SET name = '".Normalize::mysql($name)."' 
				WHERE ".self::acctAndOwnedBy($acctId,$userId).";";
		return $db->query($sql);
	}
}
?>