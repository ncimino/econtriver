<?php
class QA_Account_Modify {
	static function insert($acctName,$db) {
		$accountNameEscaped = Normalize::mysql($acctName);
		$sql = "INSERT INTO q_acct (name,active)
				VALUES ('{$accountNameEscaped}',1);";
		return $db->query($sql);
	}

	static function insertOwner($acctId,$userId,$db) {
		$sql = "INSERT INTO q_owners (acct_id,owner_id)
				VALUES ({$acctId},{$userId});";
		return $db->query($sql);
	}

	static function drop($acctId,$userId,$db) {
		$sql = "UPDATE q_acct,q_owners SET active = 0
				WHERE q_acct.id = {$acctId} 
				  AND acct_id = q_acct.id 
				  AND owner_id = {$userId};";
		return $db->query($sql);
	}

	static function restore($acctId,$userId,$db) {
		$sql = "UPDATE q_acct,q_owners
				  SET active = 1 
				WHERE q_acct.id = {$acctId} 
				  AND acct_id = q_acct.id 
				  AND owner_id = {$userId};";
		return $db->query($sql);
	}

	static function update($name,$acctId,$userId,$db) {
		$accountNameEscaped = Normalize::mysql($name);
		$sql = "UPDATE q_acct,q_owners
				  SET name = '{$accountNameEscaped}' 
				WHERE q_acct.id = {$acctId} 
				  AND acct_id = q_acct.id 
				  AND owner_id = {$userId};";
		return $db->query($sql);
	}
}
?>