<?php
class AjaxQaGetAccounts {
	function getAccountNameById($id,$db,$allowAllAccounts=FALSE) {
		if ($allowAllAccounts and ($id == 0)) {
			return "All Accounts";
		} else {
			$sql = "SELECT name FROM q_acct
        		WHERE id = {$id};";
			$db->query($sql);
			$return = $db->fetch();
			return $return['name'];
		}
	}

	function getOwnedAccounts($userId,$db) {
		$sql = "SELECT * FROM q_acct,q_owners
		        WHERE q_acct.id = acct_id 
		          AND owner_id = {$userId}
		          AND active = 1;";
		return $db->query($sql);
	}

	function getSharedAccounts($userId,$db) {
		$sql = "SELECT * FROM q_acct,q_share,q_user_groups,q_owners
		        WHERE q_share.acct_id=q_acct.id
		          AND q_user_groups.group_id=q_share.group_id
		          AND q_user_groups.user_id = {$userId}
		          AND q_user_groups.active = 1
		          AND q_acct.active = 1
		          AND q_owners.acct_id = q_acct.id
		          AND q_owners.owner_id <> {$userId}
		        GROUP BY q_acct.id;";
		return $db->query($sql);
	}

	function getDeletedAccounts($userId,$db) {
		$sql = "SELECT * FROM q_acct,q_owners
		        WHERE q_acct.id = acct_id 
		          AND owner_id = {$userId}
		          AND active = 0;";
		return $db->query($sql);
	}

	function getActiveAccounts($userId,$db) {
		$sql = "SELECT q_acct.*
				FROM q_acct,q_owners,q_share,q_user_groups
				WHERE ( q_acct.id=q_owners.acct_id
				  AND q_owners.owner_id={$userId}
				  AND q_acct.active=1 ) 
				  OR ( q_acct.id=q_share.acct_id
				  AND q_share.group_id=q_user_groups.group_id
				  AND q_user_groups.user_id={$userId}
				  AND q_user_groups.active=1
				  AND q_acct.active=1 )
				GROUP BY q_acct.id
				ORDER BY q_acct.name ASC;";
		return $db->query($sql);
	}

	function getSqlActiveAccounts($activeAccountsResult,$db) {
		$i = 0;
		$db->resetRowPointer($activeAccountsResult);
		while($result = $db->fetch($activeAccountsResult)) {
			if ($i == 0 ) $activeAccountSql = "";
			elseif ($i < $db->num($activeAccountsResult)) $activeAccountSql .= " OR ";
			$activeAccountSql .= "q_txn.acct_id = ".$result['id'];
			$i++;
		}
		return $activeAccountSql;
	}

	function getSqlAcctsToShow($showAcct=FALSE,$activeAccountsResult,$db) {
		if ($showAcct) {
			$acctsToShow = "q_txn.acct_id = ".$showAcct;
		} else {
			$acctsToShow = AjaxQaGetAccounts::getSqlActiveAccounts($activeAccountsResult,$db);		}
			return $acctsToShow;
	}
}
?>