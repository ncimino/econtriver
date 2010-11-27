<?php
class AjaxQaGroupMembers extends AjaxQaWidget {
	private $activeGroups; // MySQL result
	private $activeShares; // MySQL result
	private $inactiveShares; // MySQL result
	private $contacts; // MySQL result
	private $parentId;

	function getSplitGroupMemberClass() { return 'split_grp_acct'; }
	function getFsId() { return self::getMainClass().'_id'; }
	function getFsCloseId() { return self::getMainClass().'_close_id'; }
	function getContactClass() { return 'contact'; }
	function getContactInputId() { return self::getContactClass(); }
	function getContactId() { return self::getContactClass().'id'; }
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

	function addEntries($userId,$grpId) {
		if ($this->insertShare($userId,$grpId)) {
			$this->infoMsg->addMessage(2,'User was successfully added to Group.');
		} else {
			$this->infoMsg->addMessage(0,'This user is already associated with this group.');
		}
	}

	function addContact($contactName) {
		$userId = AjaxQaSelectGroupMembers::findContact($contactName,$this->DB);
		if (!$userId) {
			$this->infoMsg->addMessage(0,'User was not found.');
		} elseif ($this->insertContact($this->user->getUserId(),$userId)) {
			$this->insertContact($userId,$this->user->getUserId());
			$this->infoMsg->addMessage(2,'Contact was successfully added.');
		} else {
			$this->infoMsg->addMessage(0,'You are already affiliated with this contact.');
		}
	}

	function dropContact($userId) {
		if ($this->deleteContact($userId)) {
			$this->infoMsg->addMessage(2,'Contact was successfully removed.');
		}
	}

	function dropEntries($acctId,$grpId) {
		if ($this->dropShare($acctId,$grpId)) {
			$this->infoMsg->addMessage(2,'User was successfully removed from group.');
		}
	}

	function insertContact($ownerId,$contactId) {
		if ($this->DB->num(AjaxQaSelectGroupMembers::getContact($ownerId,$contactId,$this->DB)) == 0) {
			$sql = "INSERT INTO contacts (owner_id,contact_id) VALUES ('{$ownerId}','{$contactId}');";
			return $this->DB->query($sql);
		} else {
			return false;
		}
	}

	function insertShare($userId,$grpId) {
		if ($this->DB->num(AjaxQaSelectGroupMembers::getShare($userId,$grpId,$this->DB)) == 0) {
			$sql = "INSERT INTO q_user_groups (user_id,group_id,active) VALUES ('{$userId}','{$grpId}',1);";
			return $this->DB->query($sql);
		} else {
			return false;
		}
	}

	function deleteContact($userId) {
		$sql = "DELETE FROM contacts WHERE owner_id='{$this->user->getUserId()}' AND contact_id='{$userId}';";
		return $this->DB->query($sql);
	}

	function buildWidget() {
		$this->activeGroups = AjaxQaSelectGroupMembers::getActiveGroups($this->user->getUserId(),$this->DB);
		$this->contacts = AjaxQaSelectGroupMembers::getContacts($this->user->getUserId(),$this->DB);
		$divQuickAccounts = new HTMLFieldset($this->container,self::getFsId(),'manage_title');
		$lClose = new HTMLLegend($divQuickAccounts,'Group Memberships');
		$lClose->setAttribute('onclick',"hideElement('".self::getFsId()."','slow');");
		$lClose->setAttribute('title','Close');
		$aClose = new HTMLAnchor($divQuickAccounts,'#','','','');
		$aClose->setAttribute('onclick',"hideElement('".self::getFsId()."','slow');");
		$divClose = new HTMLSpan($aClose,'',self::getFsCloseId(),'ui-icon ui-icon-circle-close ui-state-red');
		$tableSplit = new Table($divQuickAccounts,1,2,'',self::getSplitGroupMemberClass());
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
			$groupId = $this->getActiveGrpId().'_'.$group['group_id'];
			$groupClass = $this->getGrpClass().' ui-droppable';
			$groupAccount = new HTMLDiv($tableListAccounts->cells[$i][0],$groupId,$groupClass);
			new HTMLParagraph($groupAccount,$group['name']);
			$this->activeShares = AjaxQaSelectGroupMembers::getAssociatedActiveContacts($group['group_id'],$this->DB);
			$this->inactiveShares = AjaxQaSelectGroupMembers::getAssociatedInactiveContacts($group['group_id'],$this->DB);
			while ($contact = $this->DB->fetch($this->activeShares)) {
				$contactId = $this->getContactId().'_'.$contact['user_id'];
				$contactClass = $this->getContactClass().' ui-draggable sub-item';
				$sharesDiv = new HTMLDiv($tableListAccounts->cells[$i][0],$contactId,$contactClass);
				$sharesP = new HTMLParagraph($sharesDiv,$contact['handle']);
			}
			while ($contact = $this->DB->fetch($this->inactiveShares)) {
				$contactId = $this->getContactId().'_'.$contact['user_id'];
				$contactClass = $this->getContactClass().' ui-draggable sub-item';
				$sharesDiv = new HTMLDiv($tableListAccounts->cells[$i][0],$contactId,$contactClass);
				$sharesP = new HTMLParagraph($sharesDiv,$contact['handle'].' (Inactive)');
			}
			$i++;
		}
	}

	function buildActiveContactsTable($parentElement) {
		if ($this->DB->num($this->contacts)>0) {
			$divContacts = new HTMLDiv($parentElement);
			$this->buildContactsTable($divContacts,'Contacts:',$this->contacts);
		}
	}

	function buildContactsTable($parentElement,$title,$queryResult) {
		new HTMLHeading($parentElement,5,$title);
		$tableListGroups = new Table($parentElement,$this->DB->num($queryResult),1);
		$i = 0;
		while ($contact = $this->DB->fetch($queryResult)) {
			$inputId = $this->getContactId().'_'.$contact['contact_id'];
			$inputClass = $this->getContactClass().' ui-draggable';
			$inputEditGroup = new HTMLDiv($tableListGroups->cells[$i][0],$inputId,$inputClass);
			$contactsP = new HTMLParagraph($inputEditGroup,$contact['handle']);
			$contactsA = new HTMLAnchor($contactsP,'#','','','');
			$contactsA->setAttribute('onclick',"QaContactDrop('quick_accounts_manage_div','{$contact['contact_id']}');");
			$contactsSpan = new HTMLSpan($contactsA,'','','ui-icon ui-icon-circle-close');
			$contactsSpan->setStyle('float: right;');
			$i++;
		}
	}

	function buildAddContactForm($parentElement) {
		$divAddContact = new HTMLDiv($parentElement);
		new HTMLHeading($divAddContact,5,'Add Contact:');
		$inputAddContact = new HTMLInputText($divAddContact,'contact','Email or User name','contact');
		$inputAddContact->setAttribute('onkeypress',"enterCall(event,function() {QaContactAdd('{$this->parentId}','".self::getContactInputId()."');})");
		$aAddContact = new HTMLAnchor($divAddContact,'#','Add Contact');
		$aAddContact->setAttribute('onclick',"QaContactAdd('{$this->parentId}','".self::getContactInputId()."');");
	}
}
?>