<?php
class QA_Account_SQL extends QA_DB_Table {
	function sharedWithUserByOwner($ownerId,$userId,$acctStatus=NULL,$shareStatus=NULL) {
		$sql = self::OWNERS.".owner_id = ".Normalize::mysql($ownerId)."
				AND ".self::sharedWithUser($userId,$acctStatus,$shareStatus);
		return $sql;
	}
	
	function sharedOnly($userId,$acctStatus=NULL,$shareStatus=NULL) {
		$sql = self::OWNERS.".owner_id <> ".Normalize::mysql($userId)."
				AND ".self::sharedWithUser($userId,$acctStatus,$shareStatus);
		return $sql;
	}
	
	function sharedWithUser($userId,$acctStatus=NULL,$shareStatus=NULL) {
		$sql = self::ACCT.".id=".self::SHARE.".acct_id
				AND ".self::SHARE.".group_id=".self::USER_GROUPS.".group_id
				AND ".self::USER_GROUPS.".user_id=".Normalize::mysql($userId)."
				AND ".self::acctAndOwner($acctStatus);
		$sql .= (isset($shareStatus)) ? " AND ".self::USER_GROUPS.".active=".Normalize::mysql($shareStatus) : "";
		return $sql;
	}
	
	function ownedBy($ownerId,$status=NULL) {
		$sql = self::OWNERS.".owner_id={$ownerId}
				AND ".self::acctAndOwner($status);
		return $sql;
	}
	
	function acctAndOwnedBy($acctId,$ownerId) {
		$sql = self::ACCT.".id = ".Normalize::mysql($acctId)."
				AND ".self::acctAndOwner()."
				AND ".self::ACCT.".id = ".Normalize::mysql($acctId);
		return $sql;
	}
	
	function acctAndOwner($status=NULL) {
		$sql = self::ACCT.".id=".self::OWNERS.".acct_id";
		$sql .= (isset($status)) ? " AND ".self::ACCT.".active=".Normalize::mysql($status) : "";
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