<?php
class QA_Account_Select extends QA_Account_SQL {
	
	const USER_IDENTIFIER = 'u';

	function nameById($id,$db,$allowAllAccounts=FALSE) {
		if (self::aUserIsSelected($id)) { //:KLUDGE: Checks to see if user was selected
			$user = User::selectUserById(self::extractUserId($id),$db);
			return $user['handle']."'s Accounts";
		} elseif ($allowAllAccounts and ($id == 0)) {
			return "All Accounts";
		} else {
			$sql = "SELECT name FROM ".self::ACCT."
        		WHERE id = {$id};";
			$db->query($sql);
			$return = $db->fetch();
			return $return['name'];
		}
	}

	function owned($userId,$db) {
		$sql = "SELECT * FROM ".self::ACCT.",".self::OWNERS."
		        WHERE ".self::ownedByWithStatus($userId).";";
		return $db->query($sql);
	}

	function shared($userId,$db) {
		$sql = "SELECT * FROM ".self::ACCT.",".self::SHARE.",".self::USER_GROUPS.",".self::OWNERS."
		        WHERE ".self::sharedOnly($userId)."
		        GROUP BY ".self::ACCT.".id;";
		return $db->query($sql);
	}

	function sharedByOwner($ownerId,$userId,$db) {
		$sql = "SELECT * FROM ".self::ACCT.",".self::SHARE.",".self::USER_GROUPS.",".self::OWNERS."
		         WHERE ".self::sharedWithUserByOwner($ownerId,$userId)."
		         GROUP BY ".self::ACCT.".id;";
		return $db->query($sql);
	}

	function deleted($userId,$db) {
		$sql = "SELECT * FROM ".self::ACCT.",".self::OWNERS."
		         WHERE ".self::ownedByWithStatus($userId,self::INACTIVE).";";
		return $db->query($sql);
	}

	function active($userId,$db) {
		$sql = "SELECT ".self::ACCT.".*,".self::OWNERS.".owner_id
				  FROM ".self::ACCT.",".self::OWNERS.",".self::SHARE.",".self::USER_GROUPS."
				 WHERE ( ".self::ownedByWithStatus($userId)." ) 
				    OR ( ".self::sharedWithUser($userId)." )
				 GROUP BY ".self::ACCT.".id
				 ORDER BY ".self::ACCT.".name ASC;";
		return $db->query($sql);
	}

	function aUserIsSelected($acct) {
		return strstr($acct,self::USER_IDENTIFIER);
	}

	function extractUserId($acct) {
		return str_replace(self::USER_IDENTIFIER,"",$acct);
	}
}
?>