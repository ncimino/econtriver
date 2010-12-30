<?php
class QA_Group_Widget extends QA_Widget {
	private $activeGroups; // MySQL result
	private $inactiveGroups; // MySQL result
	private $parentId;
	private $grpName = '';
	
	const C_CREATE = 'add_grp';
	const C_EDIT = 'group';
	const C_ACTIVE = 'active_grps';
	const C_INACTIVE = 'inactive_grps';
	
	const I_FS = 'quick_accts_id';
	const I_FS_CLOSE = 'quick_accts_close_id';
	const I_CREATE = 'add_grp_text';
	const I_EDIT = 'group_text';

	const N_CREATE = 'add_grp_name';
	const N_EDIT = 'group_name';

	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
	}

	function addEntries($name) {
		if ($escapedName = $this->checkGroupName($name)) {
			if ($this->insertGroup($escapedName) and $this->insertUserGroup()) {
				$this->infoMsg->addMessage(2,'Group was successfully created.');
			}
		} else {
			$this->grpName = $name;
		}
	}

	function updateEntries($name,$grpId) {
		if (!empty($grpId) and $sanitizedName = $this->checkGroupName($name)) {
			if ($this->updateGroup($sanitizedName,$grpId)) {
				$this->infoMsg->addMessage(2,'Group was successfully updated.');
			}
		}
	}

	function dropEntries($grpId) {
		if (!empty($grpId) and $this->dropGroup($grpId)) {
			$this->infoMsg->addMessage(2,'Group was successfully disabled.');
		}
	}

	function permDropEntries($grpId) {
		if (!empty($grpId) and $this->permDropGroup($grpId)) {
			$this->infoMsg->addMessage(2,'Successfully left group.');
		}
	}

	function rejoinEntries($grpId) {
		if (!empty($grpId) and $this->rejoinGroup($grpId)) {
			$this->infoMsg->addMessage(2,'Successfully joined group.');
		}
	}

	function checkGroupName($name) {
		if (!($sanitizedName = Normalize::sanitize($name,$this->infoMsg,$this->siteInfo))) {
			return false;
		} elseif (!(Normalize::groupNames($sanitizedName,$this->infoMsg))) {
			return false;
		} elseif ($this->getGroupByName($name)) {
			$this->infoMsg->addMessage(0,"Group '{$name}' is already in use.");
			return false;
		} else {
			return $sanitizedName;
		}
	}

	function insertGroup($grpName) {
		$grpNameEscaped = Normalize::mysql($grpName);
		$sql = "INSERT INTO q_group (name)
VALUES ('{$grpNameEscaped}');";
		return $this->DB->query($sql);
	}

	function insertUserGroup() {
		$sql = "INSERT INTO q_user_groups (group_id,user_id,active)
VALUES ({$this->DB->lastID()},{$this->user->getUserId()},1);";
		return $this->DB->query($sql);
	}

	function dropGroup($group_id) {
		$sql = "UPDATE q_user_groups SET active = 0 WHERE user_id = {$this->user->getUserId()} AND group_id = {$group_id}";
		return $this->DB->query($sql);
	}

	function permDropGroup($group_id) {
		$sql = "DELETE FROM q_user_groups WHERE user_id = {$this->user->getUserId()} AND group_id = {$group_id}";
		$dropUserGroupResult = $this->DB->query($sql);
		if ($this->getNumberOfGroupMembers($group_id) == 0) { // If there are no more members of the group, then remove it
			return $this->deleteGroup($group_id);
		} else {
			return $dropUserGroupResult;
		}
	}

	function getNumberOfGroupMembers($group_id) {
		$sql = "SELECT id FROM q_user_groups WHERE group_id = {$group_id}";
		$this->DB->query($sql);
		return $this->DB->num();
	}

	function deleteGroup($group_id) {
		$deleteGroupSharesResult = $this->deleteGroupShares($group_id);
		if ($deleteGroupSharesResult) {
			$sql = "DELETE FROM q_group WHERE id = {$group_id}";
			return $this->DB->query($sql);
		} else {
			return false;
		}
	}

	function deleteGroupShares($group_id) {
		$sql = "DELETE FROM q_share WHERE group_id = {$group_id}";
		return $this->DB->query($sql);
	}

	function rejoinGroup($group_id) {
		$sql = "UPDATE q_user_groups SET active = 1 WHERE user_id = {$this->user->getUserId()} AND group_id = {$group_id}";
		return $this->DB->query($sql);
	}

	function updateGroup($name,$id) {
		$groupNameEscaped = Normalize::mysql($name);
		$sql = "UPDATE q_group SET name = '{$groupNameEscaped}' WHERE id = {$id};";
		return $this->DB->query($sql);
	}

	function getGroupNameById($id) {
		$sql = "SELECT name FROM q_group
        WHERE id = {$id};";
		$this->DB->query($sql);
		$return = $this->DB->fetch();
		return $return['name'];
	}

	function getGroupByName($name) {
		$sql = "SELECT * FROM q_group
        WHERE name = '{$name}';";
		$this->DB->query($sql);
		return $this->DB->fetch();
	}

	function getActiveGroups() {
		$sql = "SELECT * FROM q_group,q_user_groups
        WHERE q_group.id = group_id 
          AND user_id = {$this->user->getUserId()}
          AND active = 1;";
		$this->activeGroups = $this->DB->query($sql);
	}

	function getInactiveGroups() {
		$sql = "SELECT * FROM q_group,q_user_groups
        WHERE q_group.id = group_id 
          AND user_id = {$this->user->getUserId()}
          AND active = 0;";
		$this->inactiveGroups = $this->DB->query($sql);
	}

	function createWidget() {
		$this->getActiveGroups();
		$this->getInactiveGroups();
		$divQuickAccounts = new HTML_Fieldset($this->container,self::I_FS,'manage_title');
		$lClose = new HTML_Legend($divQuickAccounts,'Group Management');
		$lClose->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		$lClose->setAttribute('title','Close');
		$aClose = new HTML_Anchor($divQuickAccounts,'#','','','');
		$aClose->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		$divClose = new HTML_Span($aClose,'',self::I_FS_CLOSE,'ui-icon ui-icon-circle-close ui-state-red');
		$this->buildCreateGroupForm($divQuickAccounts);
		$this->buildActiveGroupsTable($divQuickAccounts);
		$this->buildInactiveGroupsTable($divQuickAccounts);
		$this->printHTML();
	}

	function buildActiveGroupsTable($parentElement) {
		if ($this->DB->num($this->activeGroups)>0) {
			$divGroups = new HTML_Div($parentElement,self::C_ACTIVE);
			$this->buildGroupsTable($divGroups,'Active Groups:',$this->activeGroups,self::C_ACTIVE);
		}
	}

	function buildInactiveGroupsTable($parentElement) {
		if ($this->DB->num($this->inactiveGroups)>0) {
			$divGroups = new HTML_Div($parentElement,self::C_INACTIVE);
			$this->buildGroupsTable($divGroups,'Inactive Groups:',$this->inactiveGroups,self::C_INACTIVE,false);
		}
	}

	function buildGroupsTable($parentElement,$title,$queryResult,$tableName,$editable=true) {
		new HTML_Heading($parentElement,5,$title);
		$cols = ($editable) ? 3 : 3;
		$tableListGroups = new Table($parentElement,$this->DB->num($queryResult),$cols,$tableName);
		$i = 0;
		while ($group = $this->DB->fetch($queryResult)) {
			$groupName = (empty($group['name'])) ? $this->getGroupNameById($this->getEditGrpId()) : $group['name'];
			$inputId = $this->I_EDIT.'_'.$group['group_id'];
			$inputName = $this->N_EDIT.'_'.$group['group_id'];

			$inputEditGroup = new HTML_InputText($tableListGroups->cells[$i][0],$inputName,$groupName,$inputId,$this->C_EDIT);
			if ($editable) {
				$aEditGroup = new HTML_Anchor($tableListGroups->cells[$i][1],'#','Edit');
				$aEditGroup->setAttribute('onclick',"QaGroupEdit('{$this->parentId}','{$inputId}','{$group['group_id']}');");
				$aDropGroup = new HTML_Anchor($tableListGroups->cells[$i][2],'#','Disable');
				$aDropGroup->setAttribute('onclick',"QaGroupDrop('{$this->parentId}','{$group['group_id']}')");
			} else {
				$inputEditGroup->setAttribute('disabled',"disabled");
				$aRejoinGroup = new HTML_Anchor($tableListGroups->cells[$i][1],'#','Join');
				$aRejoinGroup->setAttribute('onclick',"QaGroupRejoin('{$this->parentId}','{$group['group_id']}');");
				$aPermDropGroup = new HTML_Anchor($tableListGroups->cells[$i][2],'#','Leave');
				$aPermDropGroup->setAttribute('onclick',"if(confirmSubmit('Are you sure you want to leave the \'".$group['name']."\' group?')) { QaGroupPermDrop('{$this->parentId}','{$group['group_id']}'); }");
			}
			$i++;
		}
	}

	function buildCreateGroupForm($parentElement) {
		$divAddGroup = new HTML_Div($parentElement,'',$this->C_CREATE);
		new HTML_Heading($divAddGroup,5,'Add Group:');
		$inputAddGroup = new HTML_InputText($divAddGroup,$this->N_CREATE(),$this->grpName,$this->I_CREATE,$this->C_CREATE);
		$inputAddGroup->setAttribute('onkeypress',"enterCall(event,function() {QaGroupAdd('{$this->parentId}','{$this->I_CREATE}');})");
		$aAddGroup = new HTML_Anchor($divAddGroup,'#','Add Group');
		$aAddGroup->setAttribute('onclick',"QaGroupAdd('{$this->parentId}','{$this->I_CREATE}');");
	}
}
?>