<?php
class AjaxQaGroupMembers extends AjaxQaWidget {
	private $activeGroups; // MySQL result
	private $activeShares; // MySQL result
	private $contacts; // MySQL result
	private $parentId;

	function getSplitGroupAcctClass() { return 'split_grp_acct'; }

	function getAcctClass() { return 'acct'; }
	function getSharedAcctId() { return self::getAcctClass().'id'; }
	function getOwnedAcctId() { return self::getAcctClass().'id'; }
	function getGrpClass() { return 'grp'; }
	function getActiveGrpId() { return self::getGrpClass().'id'; }
	function getInactiveGrpId() { return self::getGrpClass().'id'; }

	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
	}

	function addEntries($userId,$grpId) {
		if ($this->insertShare($userId,$grpId)) {
			$this->infoMsg->addMessage(2,'Group was successfully added to Account.');
		}
	}

	function dropEntries($acctId,$grpId) {
		if ($this->dropShare($acctId,$grpId)) {
			$this->infoMsg->addMessage(2,'User was successfully removed from group.');
		}
	}

	function insertShare($userId,$grpId) {
		$this->getShare($userId,$grpId);
		if ($this->DB->num() == 0) {
			$sql = "INSERT INTO q_user_groups (user_id,group_id,active) VALUES ('{$userId}','{$grpId}',1);";
			return $this->DB->query($sql);
		} else {
			$this->infoMsg->addMessage(0,'This user is already associated with this group.');
		}
	}

	function getShare($userId,$grpId) {
		$sql = "SELECT id FROM q_user_groups WHERE user_id='{$userId}' AND group_id='{$grpId}';";
		return $this->DB->query($sql);
	}

	function dropShare($userId,$grpId) {
		// This method is not currently being used.
		// If it is implemented, then a check should be added to see if the user doing the removal is also a member - security check
		/*
		$sql = "UPDATE q_user_groups
			SET active = 0
			WHERE q_user_groups.user_id = {$userId}
			AND q_user_groups.group_id = {$grpId};";
		return $this->DB->query($sql);//*/
	}

	function getActiveGroups() {
		$sql = "SELECT * FROM q_group,q_user_groups
        WHERE q_group.id = group_id 
          AND user_id = {$this->user->getUserId()}
          AND active = 1;";
		$this->activeGroups = $this->DB->query($sql);
	}
	
	function getContacts() {
		$sql = "SELECT * FROM contacts
        WHERE contacts.owner_id = {$this->user->getUserId()};";
		$this->contacts = $this->DB->query($sql);
	}

	function buildWidget() {
		$this->getActiveGroups();
		$this->getContacts();
		$fsQuickAccounts = new HTMLFieldset($this->container);
		new HTMLLegend($fsQuickAccounts,'Group Memberships');
		$tableSplit = new Table($fsQuickAccounts,1,2,'',self::getSplitGroupAcctClass());
		$this->buildActiveGroupsTable($tableSplit->cells[0][0]);
		$this->buildAddContactForm($tableSplit->cells[0][1]);
		$this->buildActiveContactsTable($tableSplit->cells[0][1]);
		$this->printHTML();
	}

	function buildActiveGroupsTable($parentElement) {
		if ($this->DB->num($this->activeGroups)>0) {
			$divActiveGroups = new HTMLDiv($parentElement);
			$this->buildGroupsTable($divActiveGroups,'Groups:',$this->activeGroups);
		}
	}

	function buildGroupsTable($parentElement,$title,$queryResult) {
		new HTMLHeading($parentElement,5,$title);
		$tableListAccounts = new Table($parentElement,$this->DB->num($queryResult),1);
		$i = 0;
		while ($group = $this->DB->fetch($queryResult)) {
			$inputId = $this->getSharedAcctId().'_'.$group['id'];
			$inputClass = $this->getAcctClass().' ui-droppable';
			$inputAccount = new HTMLDiv($tableListAccounts->cells[$i][0],$inputId,$inputClass);
			new HTMLParagraph($inputAccount,$group['name']);
			$this->getAssociatedContacts($group['id']);
			while ($contact = $this->DB->fetch()) {
				$groupId = $this->getActiveGrpId().'_'.$contact['user_id'];
				$groupClass = $this->getGrpClass().' ui-draggable';
				$sharesDiv = new HTMLDiv($tableListAccounts->cells[$i][0],$groupId,$groupClass);
				$sharesP = new HTMLParagraph($sharesDiv,$contact['handle'],'',$this->getGrpClass());
				/*
				$sharesA = new HTMLAnchor($sharesP,'#','','','');
				$sharesA->setAttribute('onclick',"QaGroupMembersDrop('quick_accounts_manage_div','{$group['id']}','{$contact['user_id']}');");
				$sharesSpan = new HTMLSpan($sharesA,'','','ui-icon ui-icon-circle-close');
				$sharesSpan->setStyle('float: right;');//*/
			}
			$i++;
		}
	}
	
	function getAssociatedContacts($grpId) {
		$sql = "SELECT q_user_groups.*,user.handle,user.user_id FROM q_user_groups,user
        WHERE q_user_groups.group_id = {$grpId}
          AND q_user_groups.user_id = user.user_id
          AND q_user_groups.active = 1;";
		$this->activeShares = $this->DB->query($sql);
	}

	function buildActiveContactsTable($parentElement) {
		if ($this->DB->num($this->contacts)>0) {
			$divContacts = new HTMLDiv($parentElement);
			$this->buildContactsTable($divContacts,'Contacts:',$this->activeGroups);
		}
	}

	function buildContactsTable($parentElement,$title,$queryResult) {
		new HTMLHeading($parentElement,5,$title);
		$tableListGroups = new Table($parentElement,$this->DB->num($queryResult),1);
		$i = 0;
		while ($contact = $this->DB->fetch($queryResult)) {
			$inputId = $this->getActiveGrpId().'_'.$contact['group_id'];
			$inputClass = $this->getGrpClass().' ui-draggable';
			$inputEditGroup = new HTMLDiv($tableListGroups->cells[$i][0],$inputId,$inputClass);
			new HTMLParagraph($inputEditGroup,$contact['name'],'',$this->getGrpClass());
			$i++;
		}
	}
	
	function buildAddContactForm($parentElement) {
		$divAddContact = new HTMLDiv($parentElement);
		new HTMLHeading($divAddContact,5,'Add Contact:');
		$inputAddContact = new HTMLInputText($divAddContact,'contact','Email or User name','contact');
		//$inputAddContact->setAttribute('onfocus',"clearField('contact','Email or User name');");
		//new HTMLScript($divAddContact,"clearField('contact','Email or User name');");
		$aAddContact = new HTMLAnchor($divAddContact,'#','Add Contact');
		$aAddContact->setAttribute('onclick',"QaContactAdd('{$this->parentId}','contact');");
	}
}
?>