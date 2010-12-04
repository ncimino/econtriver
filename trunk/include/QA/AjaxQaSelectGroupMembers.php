<?php
class QA_SelectGroupMembers {
	
	function getContact($ownerId,$userId,$db) {
		$sql = "SELECT id FROM contacts WHERE owner_id='{$ownerId}' AND contact_id='{$userId}';";
		return $db->query($sql);
	}

	function getShare($userId,$grpId,$db) {
		$sql = "SELECT id FROM q_user_groups WHERE user_id='{$userId}' AND group_id='{$grpId}';";
		return $db->query($sql);
	}
	
	function getContacts($userId,$db) {
		$sql = "SELECT contacts.*,user.handle FROM contacts,user
        WHERE contacts.owner_id = {$userId}
          AND contacts.contact_id = user.user_id
        ORDER BY user.handle ASC;";
		return $db->query($sql);
	}
	
	function getActiveGroups($userId,$db) {
		$sql = "SELECT * FROM q_group,q_user_groups
        WHERE q_group.id = group_id 
          AND user_id = {$userId}
          AND active = 1;";
		return $db->query($sql);
	}
	
	function getAssociatedActiveContacts($grpId,$db) {
		$sql = "SELECT q_user_groups.*,user.handle FROM q_user_groups,user
        WHERE q_user_groups.group_id = {$grpId}
          AND q_user_groups.user_id = user.user_id
          AND q_user_groups.active = 1
        ORDER BY user.handle ASC;";
		return $db->query($sql);
	}
	
	function getAssociatedInactiveContacts($grpId,$db) {
		$sql = "SELECT q_user_groups.*,user.handle FROM q_user_groups,user
        WHERE q_user_groups.group_id = {$grpId}
          AND q_user_groups.user_id = user.user_id
          AND q_user_groups.active = 0
        ORDER BY user.handle ASC;";
		return $db->query($sql);
	}
	
	function getAssociatedActiveContactsForAllGroups($userId,$db) {
		$activeGroupsSql = QA_SelectGroupMembers::getSqlActiveGroups(QA_SelectGroupMembers::getActiveGroups($userId,$db),$db);
		$sql = "SELECT q_user_groups.*,user.handle FROM q_user_groups,user
        WHERE ({$activeGroupsSql})
          AND q_user_groups.user_id = user.user_id
          AND q_user_groups.active = 1
        GROUP BY user.user_id
        ORDER BY user.handle ASC;";
		return (empty($activeGroupsSql)) ? FALSE : $db->query($sql);
	}
	
	function getSqlActiveGroups($activeGroupsResult,$db) {
		$i = 0;
		$db->resetRowPointer($activeGroupsResult);
		while($result = $db->fetch($activeGroupsResult)) {
			if ($i == 0 ) $activeGroupsSql = "";
			elseif ($i < $db->num($activeGroupsResult)) $activeGroupsSql .= " OR ";
			$activeGroupsSql .= "q_user_groups.group_id = ".$result['group_id'];
			$i++;
		}
		return (empty($activeGroupsSql)) ? FALSE : $activeGroupsSql;
	}

	function findContact($contactName,$db) {
		$byHandle = $db->query("SELECT user_id FROM user WHERE handle='".mysql_real_escape_string($contactName)."';");
		$byEmail = $db->query("SELECT user_id FROM user WHERE email='".mysql_real_escape_string($contactName)."';");
		if ($db->num($byHandle) > 0) {
			$fetch = $db->fetch($byHandle);
			return $fetch['user_id'];
		} elseif ($db->num($byEmail) > 0) {
			$fetch = $db->fetch($byEmail);
			return $fetch['user_id'];
		} else {
			return FALSE;
		}
	}
}
?>