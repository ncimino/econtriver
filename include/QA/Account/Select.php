<?php
class QA_Account_Select {
	
	const USER_IDENTIFIER = 'u';

	function nameById($id,$db,$allowAllAccounts=FALSE) {
		if (self::aUserIsSelected($id)) { //:KLUDGE: Checks to see if user was selected
			$user = User::selectUserById(self::extractUserId($id),$db);
			return $user['handle']."'s Accounts";
		} elseif ($allowAllAccounts and ($id == 0)) {
			return "All Accounts";
		} else {
			$sql = "SELECT name FROM ".QA_DB_Table::ACCT."
        		WHERE id = {$id};";
			$db->query($sql);
			$return = $db->fetch();
			return $return['name'];
		}
	}

	function owned($userId,$db) {
		$sql = "SELECT * FROM ".QA_DB_Table::ACCT.",".QA_DB_Table::OWNERS."
		        WHERE ".QA_DB_Table::ACCT.".id = acct_id 
		          AND owner_id = {$userId}
		          AND active = 1;";
		return $db->query($sql);
	}

	function shared($userId,$db) {
		$sql = "SELECT * FROM ".QA_DB_Table::ACCT.",".QA_DB_Table::SHARE.",".QA_DB_Table::USER_GROUPS.",".QA_DB_Table::OWNERS."
		        WHERE 
		        GROUP BY ".QA_DB_Table::ACCT.".id;";
		return $db->query($sql);
	}

	function sharedByOwner($ownerId,$userId,$db) {
		$sql = "SELECT * FROM ".QA_DB_Table::ACCT.",".QA_DB_Table::SHARE.",".QA_DB_Table::USER_GROUPS.",".QA_DB_Table::OWNERS."
		         WHERE ".self::sqlSharedWithUserByOwner($ownerId,$userId)."
		         GROUP BY ".QA_DB_Table::ACCT.".id;";
		return $db->query($sql);
	}

	function deleted($userId,$db) {
		$sql = "SELECT * FROM ".QA_DB_Table::ACCT.",".QA_DB_Table::OWNERS."
		         WHERE ".self::sqlOwnedBy($userId,QA_DB_Table::INACTIVE).";";
		return $db->query($sql);
	}

	function active($userId,$db) {
		$sql = "SELECT ".QA_DB_Table::ACCT.".*,".QA_DB_Table::OWNERS.".owner_id
				  FROM ".QA_DB_Table::ACCT.",".QA_DB_Table::OWNERS.",".QA_DB_Table::SHARE.",".QA_DB_Table::USER_GROUPS."
				 WHERE ( ".self::sqlOwnedBy($userId)." ) 
				    OR ( ".self::sqlSharedWithUser($userId)." )
				 GROUP BY ".QA_DB_Table::ACCT.".id
				 ORDER BY ".QA_DB_Table::ACCT.".name ASC;";
		return $db->query($sql);
	}
	
	function sqlSharedWithUserByOwner($ownerId,$userId,$acctActive=QA_DB_Table::ACTIVE,$shareActive=QA_DB_Table::ACTIVE) {
		$sql = QA_DB_Table::OWNERS.".owner_id = ".Normalize::mysql($ownerId)."
				AND ".self::sqlSharedWithUser($userId,$acctActive,$shareActive);
		return $sql;
	}
	
	function sqlSharedOnly() {
		$sql = QA_DB_Table::OWNERS.".owner_id <> {$userId}
				AND ".QA_DB_Table::SHARE.".acct_id=".QA_DB_Table::ACCT.".id
				AND ".QA_DB_Table::USER_GROUPS.".grpId=".QA_DB_Table::SHARE.".grpId
				AND ".QA_DB_Table::USER_GROUPS.".user_id = {$userId}
				AND ".QA_DB_Table::USER_GROUPS.".active = 1
				AND ".self::sqlLinkAcctWithOwner($active);
		return $sql;
	}
	
	function sqlSharedWithUser($userId,$acctActive=QA_DB_Table::ACTIVE,$shareActive=QA_DB_Table::ACTIVE) {
		$sql = QA_DB_Table::ACCT.".id=".QA_DB_Table::SHARE.".acct_id
				AND ".QA_DB_Table::SHARE.".grpId=".QA_DB_Table::USER_GROUPS.".grpId
				AND ".QA_DB_Table::USER_GROUPS.".user_id=".Normalize::mysql($userId)."
				AND ".QA_DB_Table::USER_GROUPS.".active=".Normalize::mysql($shareActive)."
				AND ".self::sqlLinkAcctWithOwner($acctActive);
		return $sql;
	}
	
	function sqlOwnedBy($ownerId,$active=QA_DB_Table::ACTIVE) {
		$sql = QA_DB_Table::OWNERS.".owner_id={$ownerId}
				AND ".self::sqlLinkAcctWithOwner($active);
		return $sql;
	}
	
	function sqlLinkAcctWithOwner($active=QA_DB_Table::ACTIVE) {
		$sql = QA_DB_Table::ACCT.".id=".QA_DB_Table::OWNERS.".acct_id
				AND ".QA_DB_Table::ACCT.".active=".Normalize::mysql($active);
		return $sql;
	}
	
	function sqlActive($activeAccountsResult,$db) {
		$i = 0;
		$db->resetRowPointer($activeAccountsResult);
		while($result = $db->fetch($activeAccountsResult)) {
			if ($i == 0 ) $activeAccountSql = "";
			elseif ($i < $db->num($activeAccountsResult)) $activeAccountSql .= " OR ";
			$activeAccountSql .= "q_txn.acct_id = ".$result['id'];
			$i++;
		}
		return (empty($activeAccountSql)) ? FALSE : $activeAccountSql;
	}

	function sqlActiveForOwner($ownerId,$activeAccountsResult,$db) {
		$i = 0;
		$db->resetRowPointer($activeAccountsResult);
		while($result = $db->fetch($activeAccountsResult)) {
			if ($result['owner_id'] == $ownerId) {
				if ($i == 0 ) $activeAccountSql = "";
				elseif ($i < $db->num($activeAccountsResult)) $activeAccountSql .= " OR ";
				$activeAccountSql .= "q_txn.acct_id = ".$result['id'];
				$i++;
			}
		}
		return $activeAccountSql;
	}

	function sqlAcctsToShow($selectedAcct=FALSE,$activeAccountsResult,$userId,$db) {
		if (self::aUserIsSelected($selectedAcct)) {
			$acctsToShow = self::sqlActiveForOwner(self::extractUserId($selectedAcct),$activeAccountsResult,$db);
		} elseif ($selectedAcct) {
			$acctsToShow = "q_txn.acct_id = ".$selectedAcct;
		} else {
			$acctsToShow = self::sqlActive($activeAccountsResult,$db);
		}
		return $acctsToShow;
	}

	function aUserIsSelected($acct) {
		return strstr($acct,self::USER_IDENTIFIER);
	}

	function extractUserId($acct) {
		return str_replace(self::USER_IDENTIFIER,"",$acct);
	}
}
?>