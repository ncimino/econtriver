<?php
class QA_Account_Widget extends QA_Widget {
	private $ownedAccounts; // MySQL result
	private $sharedAccounts; // MySQL result
	private $deletedAccounts; // MySQL result
	private $parentId;
	private $acctName = '';
	
	const I_FS = 'quick_accts_id';
	const I_FS_CLOSE = 'quick_accts_close_id';

	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
	}

	function addEntries($name) {
		if ($eName = $this->checkAccountName($name)) {
			if (QA_Account_Modify::insert($eName,$this->DB) and QA_Account_Modify::insertOwner($this->DB->lastID(),$this->user->getUserId(),$this->DB)) {
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
			$divQuickAccounts = new HTML_Fieldset($this->container,self::I_FS);
			$lClose = new HTML_Legend($divQuickAccounts,'Account Management',NULL,'manage_title');
			$lClose->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
			$lClose->setAttribute('title','Close');
			$aClose = new HTML_Anchor($divQuickAccounts,'#','','','');
			$aClose->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
			$divClose = new HTML_Span($aClose,'',self::I_FS_CLOSE,'ui-icon ui-icon-circle-close ui-state-red');
			QA_Account_Build::newForm($divQuickAccounts,$this->acctName,$this->parentId);
			QA_Account_Build::ownedTable($divQuickAccounts,$this->ownedAccounts,$this->parentId,$this->DB);
			QA_Account_Build::sharedTable($divQuickAccounts,$this->sharedAccounts,$this->parentId,$this->DB);
			QA_Account_Build::deletedTable($divQuickAccounts,$this->deletedAccounts,$this->parentId,$this->DB);
			$this->printHTML();
		} catch (Exception $e) {
			echo '<h1>Internal Exception:</h1><p>',  $e->getMessage(), "</p>";
		}
	}
}