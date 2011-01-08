<?php
class QA_Account_SQL extends QA_DB_Table {
	function sharedWithUserByOwner($ownerId,$userId,$acctActive=self::ACTIVE,$shareActive=self::ACTIVE) {
		$sql = self::OWNERS.".owner_id = ".Normalize::mysql($ownerId)."
				AND ".self::sharedWithUser($userId,$acctActive,$shareActive);
		return $sql;
	}
	
	function sharedOnly($userId,$acctActive=self::ACTIVE,$shareActive=self::ACTIVE) {
		$sql = self::OWNERS.".owner_id <> ".Normalize::mysql($userId)."
				AND ".self::sharedWithUser($userId,$acctActive,$shareActive);
		return $sql;
	}
	
	function sharedWithUser($userId,$acctActive=self::ACTIVE,$shareActive=self::ACTIVE) {
		$sql = self::ACCT.".id=".self::SHARE.".acct_id
				AND ".self::SHARE.".group_id=".self::USER_GROUPS.".group_id
				AND ".self::USER_GROUPS.".user_id=".Normalize::mysql($userId)."
				AND ".self::USER_GROUPS.".active=".Normalize::mysql($shareActive)."
				AND ".self::acctAndOwnerWithStatus($acctActive);
		return $sql;
	}
	
	function ownedByWithStatus($ownerId,$active=self::ACTIVE) {
		$sql = self::OWNERS.".owner_id={$ownerId}
				AND ".self::acctAndOwnerWithStatus($active);
		return $sql;
	}
	
	function acctAndOwnedBy($acctId,$ownerId) {
		$sql = self::ACCT.".id = ".Normalize::mysql($acctId)."
				AND ".self::acctAndOwner()."
				AND ".self::ACCT.".id = ".Normalize::mysql($acctId);
		return $sql;
	}
	
	function acctAndOwnerWithStatus($active=self::ACTIVE) {
		$sql = self::acctAndOwner()."
				AND ".self::ACCT.".active=".Normalize::mysql($active);
		return $sql;
	}
	
	function acctAndOwner() {
		$sql = self::ACCT.".id=".self::OWNERS.".acct_id";
		return $sql;
	}
	
	function active($activeAccountsResult,$db) {
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

	function activeForOwner($ownerId,$activeAccountsResult,$db) {
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

	function acctsToShow($selectedAcct=FALSE,$activeAccountsResult,$userId,$db) {
		if (QA_Account_Select::aUserIsSelected($selectedAcct)) {
			$acctsToShow = self::activeForOwner(QA_Account_Select::extractUserId($selectedAcct),$activeAccountsResult,$db);
		} elseif ($selectedAcct) {
			$acctsToShow = "q_txn.acct_id = ".$selectedAcct;
		} else {
			$acctsToShow = self::active($activeAccountsResult,$db);
		}
		return $acctsToShow;
	}
}
?>