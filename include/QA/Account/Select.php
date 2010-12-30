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
			$sql = "SELECT name FROM q_acct
        		WHERE id = {$id};";
			$db->query($sql);
			$return = $db->fetch();
			return $return['name'];
		}
	}

	function owned($userId,$db) {
		$sql = "SELECT * FROM q_acct,q_owners
		        WHERE q_acct.id = acct_id 
		          AND owner_id = {$userId}
		          AND active = 1;";
		return $db->query($sql);
	}

	function shared($userId,$db) {
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

	function sharedByOwner($ownerId,$userId,$db) {
		$sql = "SELECT * FROM q_acct,q_share,q_user_groups,q_owners
		        WHERE q_share.acct_id=q_acct.id
		          AND q_user_groups.group_id=q_share.group_id
		          AND q_user_groups.user_id = {$userId}
		          AND q_user_groups.active = 1
		          AND q_acct.active = 1
		          AND q_owners.acct_id = q_acct.id
		          AND q_owners.owner_id = {$ownerId}
		        GROUP BY q_acct.id;";
		return $db->query($sql);
	}

	function deleted($userId,$db) {
		$sql = "SELECT * FROM q_acct,q_owners
		        WHERE q_acct.id = acct_id 
		          AND owner_id = {$userId}
		          AND active = 0;";
		return $db->query($sql);
	}

	function active($userId,$db) {
		$sql = "SELECT q_acct.*,q_owners.owner_id
				FROM q_acct,q_owners,q_share,q_user_groups
				WHERE ( q_acct.id=q_owners.acct_id
				  AND q_owners.owner_id={$userId}
				  AND q_acct.active=1 ) 
				  OR ( q_acct.id=q_share.acct_id
				  AND q_acct.id=q_owners.acct_id
				  AND q_share.group_id=q_user_groups.group_id
				  AND q_user_groups.user_id={$userId}
				  AND q_user_groups.active=1
				  AND q_acct.active=1 )
				GROUP BY q_acct.id
				ORDER BY q_acct.name ASC;";
		return $db->query($sql);
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