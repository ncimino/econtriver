<?php
class QA_Account_Widget extends QA_Widget {
	private $ownedAccounts; // MySQL result
	private $sharedAccounts; // MySQL result
	private $deletedAccounts; // MySQL result
	private $parentId;
	private $acctName = '';

	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
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
			
			new QA_Module($this->container);
			
			QA_Account_Build::newForm($divQuickAccounts,$this->acctName,$this->parentId);
			QA_Account_Build::ownedTable($divQuickAccounts,$this->ownedAccounts,$this->parentId,$this->DB);
			QA_Account_Build::sharedTable($divQuickAccounts,$this->sharedAccounts,$this->parentId,$this->DB);
			QA_Account_Build::deletedTable($divQuickAccounts,$this->deletedAccounts,$this->parentId,$this->DB);
			$this->printHTML();
		} catch (Exception $e) { new ExceptionHandler($e); }
	}
}