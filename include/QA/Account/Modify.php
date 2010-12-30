<?php
class QA_Account_Modify {
	
	const NEW_ACCT_STATE = 1;
	
	static function insert($acctName,$db) {
		$eAccountName = Normalize::mysql($acctName);
		$sql = "INSERT INTO q_acct (name,active)
				VALUES ('{$eAccountName}',".self::NEW_ACCT_STATE.");";
		return $db->query($sql);
	}

	static function insertOwner($acctId,$userId,$db) {
		$acctIdEscaped = Normalize::mysql($acctId);
		$userIdEscaped = Normalize::mysql($userId);
		$sql = "INSERT INTO q_owners (acct_id,owner_id)
				VALUES ({$acctId},{$userId});";
		return $db->query($sql);
	}

	static function drop($acctId,$userId,$db) {
		$acctIdEscaped = Normalize::mysql($acctId);
		$userIdEscaped = Normalize::mysql($userId);
		$sql = "UPDATE q_acct,q_owners SET active = 0
				WHERE q_acct.id = {$acctIdEscaped} 
				  AND acct_id = q_acct.id 
				  AND owner_id = {$userIdEscaped};";
		return $db->query($sql);
	}

	static function restore($acctId,$userId,$db) {
		$acctIdEscaped = Normalize::mysql($acctId);
		$userIdEscaped = Normalize::mysql($userId);
		$sql = "UPDATE q_acct,q_owners
				  SET active = 1 
				WHERE q_acct.id = {$acctIdEscaped} 
				  AND acct_id = q_acct.id 
				  AND owner_id = {$userIdEscaped};";
		return $db->query($sql);
	}

	static function update($name,$acctId,$userId,$db) {
		$accountNameEscaped = Normalize::mysql($name);
		$acctIdEscaped = Normalize::mysql($acctId);
		$userIdEscaped = Normalize::mysql($userId);
		$sql = "UPDATE q_acct,q_owners
				  SET name = '{$accountNameEscaped}' 
				WHERE q_acct.id = {$acctIdEscaped} 
				  AND acct_id = q_acct.id 
				  AND owner_id = {$userIdEscaped};";
		return $db->query($sql);
	}
}
?>