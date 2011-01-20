<?php
class QA_Account_Module extends QA_Module {
	private $ownedAccounts; // MySQL result
	private $sharedAccounts; // MySQL result
	private $deletedAccounts; // MySQL result
	private $acctName = '';

	function __construct($parentId) {
		parent::__construct($parentId);
	}

	function addEntries($name) {
		if ($eName = $this->checkAccountName($name)) {
			if (QA_Account_Modify::add($eName,$this->DB) and QA_Account_Modify::addOwner($this->DB->lastID(),$this->user->getUserId(),$this->DB)) {
				$this->infoMsg->addMessage(2,'Account was successfully created.');
			}
		} else {
			$this->acctName = $name;
		}
	}

	function updateEntries($name,$acctId) {
		if (!empty($acctId) and $eName = $this->checkAccountName($name)) {
			if (QA_Account_Modify::update($eName,$acctId,$this->user->getUserId(),$this->DB)) {
				$this->infoMsg->addMessage(2,'Account was successfully updated.');
			}
		}
	}

	function dropEntries($acctId) {
		if (!empty($acctId) and QA_Account_Modify::state(QA_DB_Table::INACTIVE,$acctId,$this->user->getUserId(),$this->DB)) {
			$this->infoMsg->addMessage(2,'Account was successfully deleted.');
		}
	}

	function restoreEntries($acctId) {
		if (!empty($acctId) and QA_Account_Modify::state(QA_DB_Table::ACTIVE,$acctId,$this->user->getUserId(),$this->DB)) {
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
			
			$mgmtFrame = $this->addFrame('Account Management');
			//new QA_Module($this->container);
			
			QA_Account_Build::newForm($mgmtFrame,$this->acctName,$this->parentId);
			QA_Account_Build::ownedTable($mgmtFrame,$this->ownedAccounts,$this->parentId,$this->DB);
			QA_Account_Build::sharedTable($mgmtFrame,$this->sharedAccounts,$this->parentId,$this->DB);
			QA_Account_Build::deletedTable($mgmtFrame,$this->deletedAccounts,$this->parentId,$this->DB);
			/**/
			$this->printHTML();
		} catch (Exception $e) { new ExceptionHandler($e); }
	}
}