<?php
class QA_Group_Widget extends QA_Widget {
	private $activeGroups; // MySQL result
	private $inactiveGroups; // MySQL result
	private $grpName = '';

	function __construct($parentElement) {
		parent::__construct();
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
			if (QA_Group_Modify::update($sanitizedName,$grpId,$this->DB)) {
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

	function restoreEntries($grpId) {
		if (!empty($grpId) and QA_Group_Modify::state(QA_DB_Table::ACTIVE,$grpId,$this->user->getUserId(),$this->DB)) {
			$this->infoMsg->addMessage(2,'Successfully joined group.');
		}
	}

	function checkGroupName($name,$retry=TRUE) {
		if (!($sanitizedName = Normalize::sanitize($name,$this->infoMsg,$this->siteInfo))) {
			return false;
		} elseif (!(Normalize::groupNames($sanitizedName,$this->infoMsg))) {
			return false;
		} elseif ($group = QA_Group_Select::getGroupByName($name,$this->DB)) {
			if (QA_Group_Modify::removeIfEmpty($group['id'],$this->DB) and $retry) {
				return $this->checkGroupName($name,FALSE);
			} else { // if to . not needed, only purpose is to clean up
				$this->infoMsg->addMessage(0,"Group '{$name}' is already in use.");
				return false;
			}
		} else {
			return $sanitizedName;
		}
	}

	function createWidget() {
		$this->activeGroups = QA_Group_Select::byMember($this->user->getUserId(),$this->DB,QA_DB_Table::ACTIVE);
		$this->inactiveGroups = QA_Group_Select::byMember($this->user->getUserId(),$this->DB,QA_DB_Table::INACTIVE);
		$divQuickAccounts = new HTML_Fieldset($this->container,self::I_FS,'manage_title');
		$lClose = new HTML_Legend($divQuickAccounts,'Group Management');
		$lClose->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		$lClose->setAttribute('title','Close');
		$aClose = new HTML_Anchor($divQuickAccounts,'#','','','');
		$aClose->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		$divClose = new HTML_Span($aClose,'',self::I_FS_CLOSE,'ui-icon ui-icon-circle-close ui-state-red');
		QA_Group_Build::newForm($divQuickAccounts,$this->grpName);
		QA_Group_Build::activeTable($divQuickAccounts,$this->activeGroups,$this->DB);
		QA_Group_Build::inactiveTable($divQuickAccounts,$this->inactiveGroups,$this->DB);
		$this->printHTML();
	}

}
?>