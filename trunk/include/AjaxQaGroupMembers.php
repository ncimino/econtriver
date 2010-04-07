<?php
class AjaxQaGroupMembers extends AjaxQaWidget {
	private $activeGroups; // MySQL result
	private $activeShares; // MySQL result
	private $contacts; // MySQL result
	private $parentId;

	function getSplitGroupMemberClass() { return 'split_grp_acct'; }

	function getContactClass() { return 'contact'; }
	function getContactInputId() { return self::getContactClass(); }
	function getContactId() { return self::getContactClass().'id'; }
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
			$this->infoMsg->addMessage(2,'User was successfully added to Group.');
		} else {
			$this->infoMsg->addMessage(0,'This user is already associated with this group.');
		}
	}

	function addContact($contactName) {
		if ($this->insertContact($contactName)) {
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

	function insertContact($contactName) {
		if ($userId = $this->findContact($contactName)) {
			$this->getContact($userId);
			if ($this->DB->num() == 0) {
				$sql = "INSERT INTO contacts (owner_id,contact_id) VALUES ('{$this->user->getUserId()}','{$userId}');";
				return $this->DB->query($sql);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function findContact($contactName) {
		$byHandle = $this->DB->query("SELECT user_id FROM user WHERE handle='".mysql_real_escape_string($contactName)."';");
		$byEmail = $this->DB->query("SELECT user_id FROM user WHERE email='".mysql_real_escape_string($contactName)."';");
		if ($this->DB->num($byHandle) > 0) {
			$fetch = $this->DB->fetch($byHandle);
			return $fetch['user_id'];
		} elseif ($this->DB->num($byEmail) > 0) {
			$fetch = $this->DB->fetch($byEmail);
			return $fetch['user_id'];
		} else {
			return false;
		}
	}

	function insertShare($userId,$grpId) {
		$this->getShare($userId,$grpId);
		if ($this->DB->num() == 0) {
			$sql = "INSERT INTO q_user_groups (user_id,group_id,active) VALUES ('{$userId}','{$grpId}',1);";
			return $this->DB->query($sql);
		} else {
			return false;
		}
	}

	function getShare($userId,$grpId) {
		$sql = "SELECT id FROM q_user_groups WHERE user_id='{$userId}' AND group_id='{$grpId}';";
		return $this->DB->query($sql);
	}

	function getContact($userId) {
		$sql = "SELECT id FROM contacts WHERE owner_id='{$this->user->getUserId()}' AND contact_id='{$userId}';";
		return $this->DB->query($sql);
	}

	function deleteContact($userId) {
		$sql = "DELETE FROM contacts WHERE owner_id='{$this->user->getUserId()}' AND contact_id='{$userId}';";
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
		return false;
	}

	function getActiveGroups() {
		$sql = "SELECT * FROM q_group,q_user_groups
        WHERE q_group.id = group_id 
          AND user_id = {$this->user->getUserId()}
          AND active = 1;";
		$this->activeGroups = $this->DB->query($sql);
	}

	function getAssociatedContacts($grpId) {
		$sql = "SELECT q_user_groups.*,user.handle FROM q_user_groups,user
        WHERE q_user_groups.group_id = {$grpId}
          AND q_user_groups.user_id = user.user_id
          AND q_user_groups.active = 1;";
		$this->activeShares = $this->DB->query($sql);
	}

	function getContacts() {
		$sql = "SELECT contacts.*,user.handle FROM contacts,user
        WHERE contacts.owner_id = {$this->user->getUserId()}
          AND contacts.contact_id = user.user_id;";
		$this->contacts = $this->DB->query($sql);
	}

	function buildWidget() {
		$this->getActiveGroups();
		$this->getContacts();
		$fsQuickAccounts = new HTMLFieldset($this->container);
		new HTMLLegend($fsQuickAccounts,'Group Memberships');
		$tableSplit = new Table($fsQuickAccounts,1,2,'',self::getSplitGroupMemberClass());
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
			$this->getAssociatedContacts($group['group_id']);
			while ($contact = $this->DB->fetch()) {
				$contactId = $this->getContactId().'_'.$contact['user_id'];
				$contactClass = $this->getContactClass().' ui-draggable';
				$sharesDiv = new HTMLDiv($tableListAccounts->cells[$i][0],$contactId,$contactClass);
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
			$contactsSpan->setStyle('float: right;');//*/
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
		$aAddContact->setAttribute('onclick',"QaContactAdd('{$this->parentId}','".self::getContactInputId()."');");
	}
}
?>