<?php
class QA_Accounts extends QA_Widget {
	private $ownedAccounts; // MySQL result
	private $sharedAccounts; // MySQL result
	private $deletedAccounts; // MySQL result
	private $parentId;
	private $acctName = '';

	function getFsId() { return self::getMainClass().'_id'; }
	function getFsCloseId() { return self::getMainClass().'_close_id'; }
	function getCreateAcctClass() { return 'add_acct'; }
	function getCreateAcctInName() { return self::getCreateAcctClass().'_name'; }
	function getCreateAcctInId() { return self::getCreateAcctClass().'_text'; }
	function getEditAcctNameClass() { return 'account'; }
	function getEditAcctNameInName() { return self::getEditAcctNameClass().'_name'; }
	function getEditAcctNameInId() { return self::getEditAcctNameClass().'_text'; }
	function getSharedAcctClass() { return 'shared_accts'; }
	function getOwnedAcctClass() { return 'owned_accts'; }
	function getDeletedAcctClass() { return 'deleted_accts'; }

	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
	}

	function addEntries($name) {
		if ($escapedName = $this->checkAccountName($name)) {
			if (QA_ModifyAccounts::insertAccount($escapedName,$this->DB) and QA_ModifyAccounts::insertOwner($this->DB->lastID(),$this->user->getUserId(),$this->DB)) {
				$this->infoMsg->addMessage(2,'Account was successfully created.');
			}
		} else {
			$this->acctName = $name;
		}
	}

	function updateEntries($name,$acctId) {
		if (!empty($acctId) and $sanitizedName = $this->checkAccountName($name)) {
			if (QA_ModifyAccounts::updateAccount($sanitizedName,$acctId,$this->user->getUserId(),$this->DB)) {
				$this->infoMsg->addMessage(2,'Account was successfully updated.');
			}
		}
	}

	function dropEntries($acctId) {
		if (!empty($acctId) and QA_ModifyAccounts::dropAccount($acctId,$this->user->getUserId(),$this->DB)) {
			$this->infoMsg->addMessage(2,'Account was successfully deleted.');
		}
	}

	function restoreEntries($acctId) {
		if (!empty($acctId) and QA_ModifyAccounts::restoreAccount($acctId,$this->user->getUserId(),$this->DB)) {
			$this->infoMsg->addMessage(2,'Account was successfully restored.');
		}
	}

	function checkAccountName($name) {
		if (!($sanitizedName = Normalize::sanitize($name,$this->infoMsg,$this->siteInfo))) {
			return false;
		} elseif (!(Normalize::accountNames($sanitizedName,$this->infoMsg))) {
			return false;
		} else {
			return $sanitizedName;
		}
	}

	function buildWidget() {
		$this->ownedAccounts = QA_SelectAccounts::getOwnedAccounts($this->user->getUserId(),$this->DB);
		$this->sharedAccounts = QA_SelectAccounts::getSharedAccounts($this->user->getUserId(),$this->DB);
		$this->deletedAccounts  = QA_SelectAccounts::getDeletedAccounts($this->user->getUserId(),$this->DB);
		$divQuickAccounts = new HTML_Fieldset($this->container,self::getFsId());
		$lClose = new HTML_Legend($divQuickAccounts,'Account Management',NULL,'manage_title');
		$lClose->setAttribute('onclick',"hideElement('".self::getFsId()."','slow');");
		$lClose->setAttribute('title','Close');
		$aClose = new HTML_Anchor($divQuickAccounts,'#','','','');
		$aClose->setAttribute('onclick',"hideElement('".self::getFsId()."','slow');");
		$divClose = new HTML_Span($aClose,'',self::getFsCloseId(),'ui-icon ui-icon-circle-close ui-state-red');
		$this->buildCreateAccountForm($divQuickAccounts);
		$this->buildOwnedAccountsTable($divQuickAccounts);
		$this->buildSharedAccountsTable($divQuickAccounts);
		$this->buildDeletedAccountsTable($divQuickAccounts);
		$this->printHTML();
	}

	function buildOwnedAccountsTable($parentElement) {
		if ($this->DB->num($this->ownedAccounts)>0) {
			$divOwnedAccounts = new HTML_Div($parentElement,'',self::getOwnedAcctClass());
			$this->buildAccountsTable($divOwnedAccounts,'Owned Accounts:',$this->ownedAccounts,self::getOwnedAcctClass());
		}
	}

	function buildSharedAccountsTable($parentElement) {
		if ($this->DB->num($this->sharedAccounts)>0) {
			$divSharedAccounts = new HTML_Div($parentElement,'',self::getSharedAcctClass());
			$this->buildAccountsTable($divSharedAccounts,'Shared Accounts:',$this->sharedAccounts,self::getSharedAcctClass(),false);
		}
	}

	function buildDeletedAccountsTable($parentElement) {
		if ($this->DB->num($this->deletedAccounts)>0) {
			$divOwnedAccounts = new HTML_Div($parentElement,'',self::getDeletedAcctClass());
			$this->buildAccountsTable($divOwnedAccounts,'Deleted Accounts:',$this->deletedAccounts,self::getDeletedAcctClass(),false,true);
		}
	}

	function buildAccountsTable($parentElement,$title,$queryResult,$tableName,$editable=true,$restorable=false) {
		new HTML_Heading($parentElement,5,$title);
		$cols = ($restorable) ? 2 : 1;
		$cols = ($editable) ? 3 : $cols;
		$tableListAccounts = new Table($parentElement,$this->DB->num($queryResult),$cols,$tableName);
		$i = 0;
		while ($account = $this->DB->fetch($queryResult)) {
			$accountName = (empty($account['name'])) ? QA_SelectAccounts::getAccountNameById($this->getEditAcctId(),$this) : $account['name'];
			$inputId = $this->getEditAcctNameInId().'_'.$account['id'];
			$inputName = $this->getEditAcctNameInName().'_'.$account['id'];

			$inputEditAccount = new HTML_InputText($tableListAccounts->cells[$i][0],$inputName,$accountName,$inputId,$this->getEditAcctNameClass());
			if ($editable) {
				$jsEdit = "QaAccountEdit('{$this->parentId}','{$inputId}','{$account['id']}');";
				$jsDrop = "if(confirmSubmit('Are you sure you want to delete the \'".$account['name']."\' account?')) { QaAccountDrop('{$this->parentId}','{$account['id']}'); }";
				$aEditAccount = new HTML_Anchor($tableListAccounts->cells[$i][1],'#','Edit');
				$aEditAccount->setAttribute('onclick',$jsEdit);
				$aDropAccount = new HTML_Anchor($tableListAccounts->cells[$i][2],'#','Delete');
				$aDropAccount->setAttribute('onclick',$jsDrop);
			} elseif ($restorable) {
				$jsRestore = "QaAccountRestore('{$this->parentId}','{$account['id']}');";
				$inputEditAccount->setAttribute('disabled',"disabled");
				$aRestoreAccount = new HTML_Anchor($tableListAccounts->cells[$i][1],'#','Restore');
				$aRestoreAccount->setAttribute('onclick',$jsRestore);
			} else {
				$inputEditAccount->setAttribute('disabled',"disabled");
			}
			$i++;
		}
	}

	function buildCreateAccountForm($parentElement) {
		$divAddAccount = new HTML_Div($parentElement,'',$this->getCreateAcctClass());
		new HTML_Heading($divAddAccount,5,'Add Account:');
		$inputAddAccount = new HTML_InputText($divAddAccount,$this->getCreateAcctInName(),$this->acctName,$this->getCreateAcctInId(),$this->getCreateAcctClass());
		$inputAddAccount->setAttribute('onkeypress',"enterCall(event,function() {QaAccountAdd('{$this->parentId}','{$this->getCreateAcctInId()}');})");
		$aAddAccount = new HTML_Anchor($divAddAccount,'#','Add Account');
		$aAddAccount->setAttribute('onclick',"QaAccountAdd('{$this->parentId}','{$this->getCreateAcctInId()}');");
	}
}
?>