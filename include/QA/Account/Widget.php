<?php
class QA_Account_Widget extends QA_Widget {
	private $ownedAccounts; // MySQL result
	private $sharedAccounts; // MySQL result
	private $deletedAccounts; // MySQL result
	private $parentId;
	private $acctName = '';

	const C_CREATE = 'add_acct';
	const C_EDIT = 'account';
	const C_SHARED = 'shared_accts';
	const C_OWNED = 'owned_accts';
	const C_DELETED = 'deleted_accts';
	
	const I_FS = 'quick_accts_id';
	const I_FS_CLOSE = 'quick_accts_close_id';
	const I_CREATE = 'add_acct_text';
	const I_EDIT = 'account_text';
	
	const N_CREATE = 'add_acct_name';
	const N_EDIT = 'account_name';

	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
	}

	function addEntries($name) {
		if ($escapedName = $this->checkAccountName($name)) {
			if (QA_Account_Modify::insert($escapedName,$this->DB) and QA_Account_Modify::insertOwner($this->DB->lastID(),$this->user->getUserId(),$this->DB)) {
				$this->infoMsg->addMessage(2,'Account was successfully created.');
			}
		} else {
			$this->acctName = $name;
		}
	}

	function updateEntries($name,$acctId) {
		if (!empty($acctId) and $sanitizedName = $this->checkAccountName($name)) {
			if (QA_Account_Modify::update($sanitizedName,$acctId,$this->user->getUserId(),$this->DB)) {
				$this->infoMsg->addMessage(2,'Account was successfully updated.');
			}
		}
	}

	function dropEntries($acctId) {
		if (!empty($acctId) and QA_Account_Modify::drop($acctId,$this->user->getUserId(),$this->DB)) {
			$this->infoMsg->addMessage(2,'Account was successfully deleted.');
		}
	}

	function restoreEntries($acctId) {
		if (!empty($acctId) and QA_Account_Modify::restore($acctId,$this->user->getUserId(),$this->DB)) {
			$this->infoMsg->addMessage(2,'Account was successfully restored.');
		}
	}

	private function checkAccountName($name) {
		if (!($sanitizedName = Normalize::sanitize($name,$this->infoMsg,$this->siteInfo))) {
			return false;
		} elseif (!(Normalize::accountNames($sanitizedName,$this->infoMsg))) {
			return false;
		} else {
			return $sanitizedName;
		}
	}

	function createWidget() {
		try {
			$this->ownedAccounts = QA_Account_Select::owned($this->user->getUserId(),$this->DB);
			$this->sharedAccounts = QA_Account_Select::shared($this->user->getUserId(),$this->DB);
			$this->deletedAccounts  = QA_Account_Select::deleted($this->user->getUserId(),$this->DB);
			$divQuickAccounts = new HTML_Fieldset($this->container,I_FS);
			//*
			$lClose = new HTML_Legend($divQuickAccounts,'Account Management',NULL,'manage_title');
			$lClose->setAttribute('onclick',"hideElement('".I_FS."','slow');");
			$lClose->setAttribute('title','Close');
			$aClose = new HTML_Anchor($divQuickAccounts,'#','','','');
			$aClose->setAttribute('onclick',"hideElement('".I_FS."','slow');");
			$divClose = new HTML_Span($aClose,'',I_FS,'ui-icon ui-icon-circle-close ui-state-red');
			//*
			QA_Account_Build::buildCreateAccountForm($divQuickAccounts);
			QA_Account_Build::buildOwnedAccountsTable($divQuickAccounts,C_OWNED,$this->ownedAccounts,$this->DB);
			QA_Account_Build::buildSharedAccountsTable($divQuickAccounts,C_SHARED,$this->sharedAccounts,$this->DB);
			QA_Account_Build::buildDeletedAccountsTable($divQuickAccounts,C_DELETED,$this->deletedAccounts,$this->DB);
			//*/
			$this->printHTML();
		} catch (Exception $e) {
			echo '<h1>Internal Exception:</h1><p>',  $e->getMessage(), "</p>";
		}
	}
}