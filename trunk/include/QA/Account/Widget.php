<?php
class QA_Account_Widget extends QA_Widget {
	private $ownedAccounts; // MySQL result
	private $sharedAccounts; // MySQL result
	private $deletedAccounts; // MySQL result
	private $parentId;
	private $acctName = '';

	//const editAcctNameClass = 'account';
	
	const CLASSES = array (	'create'=>'add_acct',
							'edit'=>'account',
							'shared'=>'shared_accts',
							'owned'=>'owned_accts',
							'deleted'=>'deleted_accts',
							);
							//self::MAIN_CLASS <= quick_accts
	const IDS = array (		'fs'=>'quick_accts_id',
							'fs_close'=>'quick_accts_close_id',					
							'create'=>'add_acct_text',					
							'edit'=>'account_text',
							);		
	const NAMES = array (	'create'=>'add_acct_name',
							'edit'=>'account_name',					
							);
						
	//function getFsId() { return self::getMainClass().'_id'; }
	//function getFsCloseId() { return self::getMainClass().'_close_id'; }
	//function getCreateAcctClass() { return 'add_acct'; }
	//function getCreateAcctInName() { return self::getCreateAcctClass().'_name'; }
	//function getCreateAcctInId() { return self::getCreateAcctClass().'_text'; }
	// function getEditAcctNameClass() { return 'account'; }
	//function getEditAcctNameInName() { return self::editAcctNameClass.'_name'; }
	//function getEditAcctNameInId() { return self::editAcctNameClass.'_text'; }
	//function getSharedAcctClass() { return 'shared_accts'; }
	//function getOwnedAcctClass() { return 'owned_accts'; }
	//function getDeletedAcctClass() { return 'deleted_accts'; }

	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
	}

	function addEntries($name) {
		if ($escapedName = $this->checkAccountName($name)) {
			if (QA_Account_Modifier::insertAccount($escapedName,$this->DB) and QA_Account_Modifier::insertOwner($this->DB->lastID(),$this->user->getUserId(),$this->DB)) {
				$this->infoMsg->addMessage(2,'Account was successfully created.');
			}
		} else {
			$this->acctName = $name;
		}
	}

	function updateEntries($name,$acctId) {
		if (!empty($acctId) and $sanitizedName = $this->checkAccountName($name)) {
			if (QA_Account_Modifier::updateAccount($sanitizedName,$acctId,$this->user->getUserId(),$this->DB)) {
				$this->infoMsg->addMessage(2,'Account was successfully updated.');
			}
		}
	}

	function dropEntries($acctId) {
		if (!empty($acctId) and QA_Account_Modifier::dropAccount($acctId,$this->user->getUserId(),$this->DB)) {
			$this->infoMsg->addMessage(2,'Account was successfully deleted.');
		}
	}

	function restoreEntries($acctId) {
		if (!empty($acctId) and QA_Account_Modifier::restoreAccount($acctId,$this->user->getUserId(),$this->DB)) {
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
		$this->ownedAccounts = QA_Account_Selector::getOwnedAccounts($this->user->getUserId(),$this->DB);
		$this->sharedAccounts = QA_Account_Selector::getSharedAccounts($this->user->getUserId(),$this->DB);
		$this->deletedAccounts  = QA_Account_Selector::getDeletedAccounts($this->user->getUserId(),$this->DB);
		$divQuickAccounts = new HTML_Fieldset($this->container,self::getFsId());
		$lClose = new HTML_Legend($divQuickAccounts,'Account Management',NULL,'manage_title');
		$lClose->setAttribute('onclick',"hideElement('".self::getFsId()."','slow');");
		$lClose->setAttribute('title','Close');
		$aClose = new HTML_Anchor($divQuickAccounts,'#','','','');
		$aClose->setAttribute('onclick',"hideElement('".self::getFsId()."','slow');");
		$divClose = new HTML_Span($aClose,'',self::getFsCloseId(),'ui-icon ui-icon-circle-close ui-state-red');
		QA_Account_Builder::buildCreateAccountForm($divQuickAccounts);
		if ($this->DB->num($this->ownedAccounts)>0) QA_Account_Builder::buildOwnedAccountsTable($divQuickAccounts,self::get(CLASSES,'owned'),$this->ownedAccounts);
		if ($this->DB->num($this->sharedAccounts)>0) QA_Account_Builder::buildSharedAccountsTable($divQuickAccounts,self::get(CLASSES,'shared'),$this->sharedAccounts);
		if ($this->DB->num($this->deletedAccounts)>0) QA_Account_Builder::buildDeletedAccountsTable($divQuickAccounts,self::get(CLASSES,'deleted'),$this->deletedAccounts);
		$this->printHTML();
	}
}
?>