<?php
class QA_SharedAccounts extends QA_Widget {
	private $ownedAccounts; // MySQL result
	private $sharedAccounts; // MySQL result
	private $activeShares; // MySQL result
	private $activeGroups; // MySQL result
	private $contactGroups; // MySQL result
	private $parentId;

	function getSplitGroupAcctClass() { return 'split_grp_acct'; }
	function getFsId() { return self::getMainClass().'_id'; }
	function getFsCloseId() { return self::getMainClass().'_close_id'; }
	function getAcctClass() { return 'account'; }
	function getSharedAcctId() { return self::getAcctClass().'id'; }
	function getOwnedAcctId() { return self::getAcctClass().'id'; }
	function getGrpClass() { return 'group'; }
	function getActiveGrpId() { return self::getGrpClass().'id'; }
	function getInactiveGrpId() { return self::getGrpClass().'id'; }

	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
	}

	function addEntries($acctId,$grpId) {
		if ($this->insertShare($acctId,$grpId)) {
			$this->infoMsg->addMessage(2,'Group was successfully added to Account.');
		}
	}

	function dropEntries($acctId,$grpId) {
		if ($this->dropShare($acctId,$grpId)) {
			$this->infoMsg->addMessage(2,'Group was successfully removed from account.');
		}
	}

	function insertShare($acctId,$grpId) {
		$this->getShare($acctId,$grpId);
		if ($this->DB->num() == 0) {
			$sql = "INSERT INTO q_share (acct_id,group_id) VALUES ('{$acctId}','{$grpId}');";
			return $this->DB->query($sql);
		} else {
			$this->infoMsg->addMessage(0,'This group is already associated with this account.');
		}
	}

	function getShare($acctId,$grpId) {
		$sql = "SELECT id FROM q_share WHERE acct_id='{$acctId}' AND group_id='{$grpId}';";
		return $this->DB->query($sql);
	}

	function dropShare($acctId,$grpId) {
		$sql = "DELETE q_share.*
			FROM q_share,q_owners
			WHERE q_share.acct_id = {$acctId}
			AND q_share.group_id = {$grpId}
			AND q_owners.acct_id = q_share.acct_id
			AND q_owners.owner_id = {$this->user->getUserId()};";
		return $this->DB->query($sql);
	}

	function getOwnedAccounts() {
		$sql = "SELECT * FROM q_acct,q_owners
        WHERE q_acct.id = acct_id 
          AND owner_id = {$this->user->getUserId()}
          AND active = 1;";
		$this->ownedAccounts = $this->DB->query($sql);
	}

	function getSharedAccounts() {
		$sql = "SELECT * FROM q_acct,q_share,q_user_groups,q_owners
        WHERE q_share.acct_id=q_acct.id
          AND q_user_groups.group_id=q_share.group_id
          AND q_user_groups.user_id = {$this->user->getUserId()}
          AND q_user_groups.active = 1
          AND q_acct.active = 1
          AND q_owners.acct_id = q_acct.id
          AND q_owners.owner_id <> {$this->user->getUserId()}
        GROUP BY q_acct.id;";
		$this->sharedAccounts = $this->DB->query($sql);
	}

	function getActiveGroups() {
		$sql = "SELECT * FROM q_group,q_user_groups
        WHERE q_group.id = group_id 
          AND user_id = {$this->user->getUserId()}
          AND active = 1;";
		$this->activeGroups = $this->DB->query($sql);
	}

	function getContactGroups() {
		$sql = "SELECT * FROM contacts,q_user_groups,q_group
        WHERE owner_id = {$this->user->getUserId()}
          AND contact_id = q_user_groups.user_id
          AND q_user_groups.group_id = q_group.id
          AND q_user_groups.active = 1
        GROUP BY q_group.id;";
		$this->contactGroups = $this->DB->query($sql);
	}

	function getActiveShares($acctId) {
		$sql = "SELECT * FROM q_share,q_group
        WHERE q_group.id = group_id 
          AND acct_id = {$acctId}
        ORDER BY name ASC;";
		$this->activeShares = $this->DB->query($sql);
	}

	function buildWidget() {
		$this->getContactGroups();
		$this->getActiveGroups();
		$this->getSharedAccounts();
		$this->getOwnedAccounts();
		$divQuickAccounts = new HTML_Fieldset($this->container,self::getFsId(),'manage_title');		
		$lClose = new HTML_Legend($divQuickAccounts,'Account Sharing');
		$lClose->setAttribute('onclick',"hideElement('".self::getFsId()."','slow');");
		$lClose->setAttribute('title','Close');
		$aClose = new HTML_Anchor($divQuickAccounts,'#','','','');
		$aClose->setAttribute('onclick',"hideElement('".self::getFsId()."','slow');");
		$divClose = new HTML_Span($aClose,'',self::getFsCloseId(),'ui-icon ui-icon-circle-close ui-state-red');
		$tableSplit = new Table($divQuickAccounts,1,3,'',self::getSplitGroupAcctClass());
		$this->buildOwnedAccountsTable($tableSplit->cells[0][0]);
		$this->buildSharedAccountsTable($tableSplit->cells[0][0]);
		$this->buildActiveGroupsTable($tableSplit->cells[0][1]);
		$this->buildContactGroupsTable($tableSplit->cells[0][2]);
		$this->printHTML();
	}

	function buildOwnedAccountsTable($parentElement) {
		if ($this->DB->num($this->ownedAccounts)>0) {
			$divOwnedAccounts = new HTML_Div($parentElement);
			$this->buildAccountsTable($divOwnedAccounts,'Owned Accounts:',$this->ownedAccounts,true);
		}
	}

	function buildSharedAccountsTable($parentElement) {
		if ($this->DB->num($this->sharedAccounts)>0) {
			$divSharedAccounts = new HTML_Div($parentElement);
			$this->buildAccountsTable($divSharedAccounts,'Shared Accounts:',$this->sharedAccounts,false);
		}
	}

	function buildAccountsTable($parentElement,$title,$queryResult,$allowEditing) {
		new HTML_Heading($parentElement,5,$title);
		$tableListAccounts = new Table($parentElement,$this->DB->num($queryResult),1);
		$i = 0;
		while ($account = $this->DB->fetch($queryResult)) {
			$inputId = $this->getSharedAcctId().'_'.$account['id'];
			$inputClass = ($allowEditing) ? $this->getAcctClass().' ui-droppable' : $this->getAcctClass();
			$inputAccount = new HTML_Div($tableListAccounts->cells[$i][0],$inputId,$inputClass);
			new HTML_Paragraph($inputAccount,$account['name']);
			$this->getActiveShares($account['id']);
			while ($group = $this->DB->fetch()) {
				$groupId = $this->getActiveGrpId().'_'.$group['group_id'];
				$groupClass = $this->getGrpClass().' ui-draggable sub-item';
				$sharesDiv = new HTML_Div($tableListAccounts->cells[$i][0],$groupId,$groupClass);
				$sharesP = new HTML_Paragraph($sharesDiv,$group['name']);
				if ($allowEditing) {
					$sharesA = new HTML_Anchor($sharesP,'#','','','');
					$sharesA->setAttribute('onclick',"QaSharedAccountsDrop('quick_accounts_manage_div','{$group['group_id']}','{$account['id']}');");
					$sharesSpan = new HTML_Span($sharesA,'','','ui-icon ui-icon-circle-close');
					$sharesSpan->setStyle('float: right;');
				}
			}
			$i++;
		}
	}

	function buildActiveGroupsTable($parentElement) {
		if ($this->DB->num($this->activeGroups)>0) {
			$divGroups = new HTML_Div($parentElement);
			$this->buildGroupsTable($divGroups,'Active Groups:',$this->activeGroups);
		}
	}

	function buildContactGroupsTable($parentElement) {
		if ($this->DB->num($this->contactGroups)>0) {
			$divGroups = new HTML_Div($parentElement);
			$this->buildGroupsTable($divGroups,'Contacts belong to:',$this->contactGroups);
		}
	}

	function buildGroupsTable($parentElement,$title,$queryResult) {
		new HTML_Heading($parentElement,5,$title);
		$tableListGroups = new Table($parentElement,$this->DB->num($queryResult),1);
		$i = 0;
		while ($group = $this->DB->fetch($queryResult)) {
			$inputId = $this->getActiveGrpId().'_'.$group['group_id'];
			$inputClass = $this->getGrpClass().' ui-draggable';
			$inputEditGroup = new HTML_Div($tableListGroups->cells[$i][0],$inputId,$inputClass);
			new HTML_Paragraph($inputEditGroup,$group['name']);
			$i++;
		}
	}
}
?>