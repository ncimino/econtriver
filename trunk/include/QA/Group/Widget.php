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
	
	const I_FS = 'qa_id';
	const I_FS_CLOSE = 'qa_close_id';
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
			if (QA_Group_Modify::add($escapedName,$this->DB) and QA_Group_Modify::addUser($this->DB->lastID(),$this->user->getUserId(),$this->DB)) {
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
		if (!empty($grpId) and QA_Group_Modify::state(QA_DB_Table::INACTIVE,$grpId,$this->user->getUserId(),$this->DB)) {
			$this->infoMsg->addMessage(2,'Group was successfully disabled.');
		}
	}

	function permDropEntries($grpId) {
		if (!empty($grpId) and QA_Group_Modify::dropUser($grpId,$this->user->getUserId(),$this->DB)) {
			QA_Group_Modify::removeIfEmpty($grpId,$this->DB);
			$this->infoMsg->addMessage(2,'Successfully left group.');
		}
	}

	function rejoinEntries($grpId) {
		if (!empty($grpId) and QA_Group_Modify::state(QA_DB_Table::ACTIVE,$grpId,$this->user->getUserId(),$this->DB)) {
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
			$inputId = $this->I_EDIT.'_'.$group['grpId'];
			$inputName = $this->N_EDIT.'_'.$group['grpId'];

			$inputEditGroup = new HTML_InputText($tableListGroups->cells[$i][0],$inputName,$groupName,$inputId,$this->C_EDIT);
			if ($editable) {
				$aEditGroup = new HTML_Anchor($tableListGroups->cells[$i][1],'#','Edit');
				$aEditGroup->setAttribute('onclick',"QaGroupEdit('{$this->parentId}','{$inputId}','{$group['grpId']}');");
				$aDropGroup = new HTML_Anchor($tableListGroups->cells[$i][2],'#','Disable');
				$aDropGroup->setAttribute('onclick',"QaGroupDrop('{$this->parentId}','{$group['grpId']}')");
			} else {
				$inputEditGroup->setAttribute('disabled',"disabled");
				$aRejoinGroup = new HTML_Anchor($tableListGroups->cells[$i][1],'#','Join');
				$aRejoinGroup->setAttribute('onclick',"QaGroupRejoin('{$this->parentId}','{$group['grpId']}');");
				$aPermDropGroup = new HTML_Anchor($tableListGroups->cells[$i][2],'#','Leave');
				$aPermDropGroup->setAttribute('onclick',"if(confirmSubmit('Are you sure you want to leave the \'".$group['name']."\' group?')) { QaGroupPermDrop('{$this->parentId}','{$group['grpId']}'); }");
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