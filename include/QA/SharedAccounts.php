<?php
class QA_SharedAccounts extends QA_Widget {
	private $ownedAccounts; // MySQL result
	private $sharedAccounts; // MySQL result
	private $activeShares; // MySQL result
	private $activeGroups; // MySQL result
	private $contactGroups; // MySQL result
	private $parentId;

	function getSplitGroupAcctClass() { return 'split_grp_acct'; }
	function I_FS { return C_MAIN.'_id'; }
	function I_FS_CLOSE { return C_MAIN.'_close_id'; }
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
			$sql = "INSERT INTO ".QA_DB_Table::SHARE." (acct_id,grpId) VALUES ('{$acctId}','{$grpId}');";
			return $this->DB->query($sql);
		} else {
			$this->infoMsg->addMessage(0,'This group is already associated with this account.');
		}
	}

	function getShare($acctId,$grpId) {
		$sql = "SELECT id FROM ".QA_DB_Table::SHARE." WHERE acct_id='{$acctId}' AND grpId='{$grpId}';";
		return $this->DB->query($sql);
	}

	function dropShare($acctId,$grpId) {
		$sql = "DELETE ".QA_DB_Table::SHARE.".*
			FROM ".QA_DB_Table::SHARE.",".QA_DB_Table::OWNERS."
			WHERE ".QA_DB_Table::SHARE.".acct_id = {$acctId}
			AND ".QA_DB_Table::SHARE.".grpId = {$grpId}
			AND ".QA_DB_Table::OWNERS.".acct_id = ".QA_DB_Table::SHARE.".acct_id
			AND ".QA_DB_Table::OWNERS.".owner_id = {$this->user->getUserId()};";
		return $this->DB->query($sql);
	}

	function ownedAccounts() {
		$sql = "SELECT * FROM ".QA_DB_Table::ACCT.",".QA_DB_Table::OWNERS."
        WHERE ".QA_DB_Table::ACCT.".id = acct_id 
          AND owner_id = {$this->user->getUserId()}
          AND active = 1;";
		$this->ownedAccounts = $this->DB->query($sql);
	}

	function sharedAccounts() {
		$sql = "SELECT * FROM ".QA_DB_Table::ACCT.",".QA_DB_Table::SHARE.",".QA_DB_Table::USER_GROUPS.",".QA_DB_Table::OWNERS."
        WHERE ".QA_DB_Table::SHARE.".acct_id=".QA_DB_Table::ACCT.".id
          AND ".QA_DB_Table::USER_GROUPS.".grpId=".QA_DB_Table::SHARE.".grpId
          AND ".QA_DB_Table::USER_GROUPS.".user_id = {$this->user->getUserId()}
          AND ".QA_DB_Table::USER_GROUPS.".active = 1
          AND ".QA_DB_Table::ACCT.".active = 1
          AND ".QA_DB_Table::OWNERS.".acct_id = ".QA_DB_Table::ACCT.".id
          AND ".QA_DB_Table::OWNERS.".owner_id <> {$this->user->getUserId()}
        GROUP BY ".QA_DB_Table::ACCT.".id;";
		$this->sharedAccounts = $this->DB->query($sql);
	}

	function getActiveGroups() {
		$sql = "SELECT * FROM ".QA_DB_Table::GROUP.",".QA_DB_Table::USER_GROUPS."
        WHERE ".QA_DB_Table::GROUP.".id = grpId 
          AND user_id = {$this->user->getUserId()}
          AND active = 1;";
		$this->activeGroups = $this->DB->query($sql);
	}

	function getContactGroups() {
		$sql = "SELECT * FROM contacts,".QA_DB_Table::USER_GROUPS.",".QA_DB_Table::GROUP."
        WHERE owner_id = {$this->user->getUserId()}
          AND contact_id = ".QA_DB_Table::USER_GROUPS.".user_id
          AND ".QA_DB_Table::USER_GROUPS.".grpId = ".QA_DB_Table::GROUP.".id
          AND ".QA_DB_Table::USER_GROUPS.".active = 1
        GROUP BY ".QA_DB_Table::GROUP.".id;";
		$this->contactGroups = $this->DB->query($sql);
	}

	function getActiveShares($acctId) {
		$sql = "SELECT * FROM ".QA_DB_Table::SHARE.",".QA_DB_Table::GROUP."
        WHERE ".QA_DB_Table::GROUP.".id = grpId 
          AND acct_id = {$acctId}
        ORDER BY name ASC;";
		$this->activeShares = $this->DB->query($sql);
	}

	function createWidget() {
		$this->getContactGroups();
		$this->getActiveGroups();
		$this->sharedAccounts();
		$this->ownedAccounts();
		$divQuickAccounts = new HTML_Fieldset($this->container,self::I_FS,'manage_title');		
		$lClose = new HTML_Legend($divQuickAccounts,'Account Sharing');
		$lClose->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		$lClose->setAttribute('title','Close');
		$aClose = new HTML_Anchor($divQuickAccounts,'#','','','');
		$aClose->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		$divClose = new HTML_Span($aClose,'',self::I_FS_CLOSE,'ui-icon ui-icon-circle-close ui-state-red');
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
				$grpId = $this->getActiveGrpId().'_'.$group['grpId'];
				$groupClass = $this->getGrpClass().' ui-draggable sub-item';
				$sharesDiv = new HTML_Div($tableListAccounts->cells[$i][0],$grpId,$groupClass);
				$sharesP = new HTML_Paragraph($sharesDiv,$group['name']);
				if ($allowEditing) {
					$sharesA = new HTML_Anchor($sharesP,'#','','','');
					$sharesA->setAttribute('onclick',"QaSharedAccountsDrop('qa_mng_div','{$group['grpId']}','{$account['id']}');");
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
			$inputId = $this->getActiveGrpId().'_'.$group['grpId'];
			$inputClass = $this->getGrpClass().' ui-draggable';
			$inputEditGroup = new HTML_Div($tableListGroups->cells[$i][0],$inputId,$inputClass);
			new HTML_Paragraph($inputEditGroup,$group['name']);
			$i++;
		}
	}
}
?>