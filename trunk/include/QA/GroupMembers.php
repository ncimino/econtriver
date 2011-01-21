<?php
class QA_GroupMembers extends QA_Module {
	private $activeGroups; // MySQL result
	private $activeShares; // MySQL result
	private $inactiveShares; // MySQL result
	private $contacts; // MySQL result

	function getSplitGroupMemberClass() { return 'split_grp_acct'; }
	function I_FS { return C_MAIN.'_id'; }
	function I_FS_CLOSE { return C_MAIN.'_close_id'; }
	function getContactClass() { return 'contact'; }
	function getContactInputId() { return self::getContactClass(); }
	function getContactId() { return self::getContactClass().'id'; }
	function getGrpClass() { return 'group'; }
	function getActiveGrpId() { return self::getGrpClass().'id'; }
	function getInactiveGrpId() { return self::getGrpClass().'id'; }

	function __construct() {
		parent::__construct();
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
		$userId = QA_SelectGroupMembers::findContact($contactName,$this->DB);
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
		if ($this->DB->num(QA_SelectGroupMembers::getContact($ownerId,$contactId,$this->DB)) == 0) {
			$sql = "INSERT INTO contacts (owner_id,contact_id) VALUES ('{$ownerId}','{$contactId}');";
			return $this->DB->query($sql);
		} else {
			return false;
		}
	}

	function insertShare($userId,$grpId) {
		if ($this->DB->num(QA_SelectGroupMembers::getShare($userId,$grpId,$this->DB)) == 0) {
			$sql = "INSERT INTO ".QA_DB_Table::USER_GROUPS." (user_id,grpId,active) VALUES ('{$userId}','{$grpId}',1);";
			return $this->DB->query($sql);
		} else {
			return false;
		}
	}

	function deleteContact($userId) {
		$sql = "DELETE FROM contacts WHERE owner_id='{$this->user->getUserId()}' AND contact_id='{$userId}';";
		return $this->DB->query($sql);
	}

	function createModule() {
		$this->activeGroups = QA_SelectGroupMembers::getActiveGroups($this->user->getUserId(),$this->DB);
		$this->contacts = QA_SelectGroupMembers::getContacts($this->user->getUserId(),$this->DB);
		$divQuickAccounts = new HTML_Fieldset($this->container,self::I_FS,'manage_title');
		$lClose = new HTML_Legend($divQuickAccounts,'Group Memberships');
		$lClose->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		$lClose->setAttribute('title','Close');
		$aClose = new HTML_Anchor($divQuickAccounts,'#','','','');
		$aClose->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		$divClose = new HTML_Span($aClose,'',self::I_FS_CLOSE,'ui-icon ui-icon-circle-close ui-state-red');
		$tableSplit = new Table($divQuickAccounts,1,2,'',self::getSplitGroupMemberClass());
		$this->activeTable($tableSplit->cells[0][0]);
		$this->buildAddContactForm($tableSplit->cells[0][1]);
		$this->buildActiveContactsTable($tableSplit->cells[0][1]);
		$this->printHTML();
	}

	function activeTable($parentElement) {
		if ($this->DB->num($this->activeGroups)>0) {
			$divActiveGroups = new HTML_Div($parentElement);
			$this->table($divActiveGroups,'Groups:',$this->activeGroups);
		}
	}

	function table($parentElement,$title,$queryResult) {
		new HTML_Heading($parentElement,5,$title);
		$tableListAccounts = new Table($parentElement,$this->DB->num($queryResult),1);
		$i = 0;
		while ($group = $this->DB->fetch($queryResult)) {
			$grpId = $this->getActiveGrpId().'_'.$group['grpId'];
			$groupClass = $this->getGrpClass().' ui-droppable';
			$groupAccount = new HTML_Div($tableListAccounts->cells[$i][0],$grpId,$groupClass);
			new HTML_Paragraph($groupAccount,$group['name']);
			$this->activeShares = QA_SelectGroupMembers::getAssociatedActiveContacts($group['grpId'],$this->DB);
			$this->inactiveShares = QA_SelectGroupMembers::getAssociatedInactiveContacts($group['grpId'],$this->DB);
			while ($contact = $this->DB->fetch($this->activeShares)) {
				$contactId = $this->getContactId().'_'.$contact['user_id'];
				$contactClass = $this->getContactClass().' ui-draggable sub-item';
				$sharesDiv = new HTML_Div($tableListAccounts->cells[$i][0],$contactId,$contactClass);
				$sharesP = new HTML_Paragraph($sharesDiv,$contact['handle']);
			}
			while ($contact = $this->DB->fetch($this->inactiveShares)) {
				$contactId = $this->getContactId().'_'.$contact['user_id'];
				$contactClass = $this->getContactClass().' ui-draggable sub-item';
				$sharesDiv = new HTML_Div($tableListAccounts->cells[$i][0],$contactId,$contactClass);
				$sharesP = new HTML_Paragraph($sharesDiv,$contact['handle'].' (Inactive)');
			}
			$i++;
		}
	}

	function buildActiveContactsTable($parentElement) {
		if ($this->DB->num($this->contacts)>0) {
			$divContacts = new HTML_Div($parentElement);
			$this->buildContactsTable($divContacts,'Contacts:',$this->contacts);
		}
	}

	function buildContactsTable($parentElement,$title,$queryResult) {
		new HTML_Heading($parentElement,5,$title);
		$tableListGroups = new Table($parentElement,$this->DB->num($queryResult),1);
		$i = 0;
		while ($contact = $this->DB->fetch($queryResult)) {
			$inputId = $this->getContactId().'_'.$contact['contact_id'];
			$inputClass = $this->getContactClass().' ui-draggable';
			$inputEditGroup = new HTML_Div($tableListGroups->cells[$i][0],$inputId,$inputClass);
			$contactsP = new HTML_Paragraph($inputEditGroup,$contact['handle']);
			$contactsA = new HTML_Anchor($contactsP,'#','','','');
			$contactsA->setAttribute('onclick',"QaContactDrop('qa_mng_div','{$contact['contact_id']}');");
			$contactsSpan = new HTML_Span($contactsA,'','','ui-icon ui-icon-circle-close');
			$contactsSpan->setStyle('float: right;');
			$i++;
		}
	}

	function buildAddContactForm($parentElement) {
		$divAddContact = new HTML_Div($parentElement);
		new HTML_Heading($divAddContact,5,'Add Contact:');
		$inputAddContact = new HTML_InputText($divAddContact,'contact','Email or User name','contact');
		/*parentid*/$inputAddContact->setAttribute('onkeypress',"enterCall(event,function() {QaContactAdd('{$this->parentId}','".self::getContactInputId()."');})");
		$aAddContact = new HTML_Anchor($divAddContact,'#','Add Contact');
		/*parentid*/$aAddContact->setAttribute('onclick',"QaContactAdd('{$this->parentId}','".self::getContactInputId()."');");
	}
}
?>