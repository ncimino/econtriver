<?php
class AjaxQaAccounts extends AjaxQaWidget {
	private $ownedAccounts; // MySQL result
	private $sharedAccounts; // MySQL result
	private $parentId;
	private $acctName = '';

	function getCreateAcctClass() { return 'add_acct'; }
	function getCreateAcctInName() { return self::getCreateAcctClass().'_name'; }
	function getCreateAcctInId() { return self::getCreateAcctClass().'_text'; }
	function getEditAcctNameClass() { return 'edit_name'; }
	function getEditAcctNameInName() { return self::getEditAcctNameClass().'_name'; }
	function getEditAcctNameInId() { return self::getEditAcctNameClass().'_text'; }
	function getSharedAcctClass() { return 'shared_accts'; }
	function getOwnedAcctClass() { return 'owned_accts'; }

	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
	}

	function addEntries($name) {
		if ($escapedName = $this->checkAccountName($name)) {
			if ($this->insertAccount($escapedName) and $this->insertOwner()) {
				$this->infoMsg->addMessage(2,'Account was successfully created.');
			}
		} else {
			$this->acctName = $name;
		}
	}

	function updateEntries($name,$acctId) {
		if (!empty($acctId) and $sanitizedName = $this->checkAccountName($name)) {
			if ($this->updateAccount($sanitizedName,$acctId)) {
				$this->infoMsg->addMessage(2,'Account was successfully updated.');
			}
		}
	}

	function dropEntries($acctId) {
		if (!empty($acctId) and $this->dropAccount($acctId)) {
			$this->infoMsg->addMessage(2,'Account was successfully deleted.');
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

	function insertAccount($acctName) {
		$accountNameEscaped = Normalize::mysql($acctName);
		$sql = "INSERT INTO q_acct (name,active)
VALUES ('{$accountNameEscaped}',1);";
		return $this->DB->query($sql);
	}

	function insertOwner() {
		$sql = "INSERT INTO q_owners (acct_id,owner_id)
VALUES ({$this->DB->lastID()},{$this->user->getUserId()});";
		return $this->DB->query($sql);
	}

	function dropAccount($acctId) {
		$sql = "UPDATE q_acct,q_owners SET active = 0 WHERE q_acct.id = {$acctId} AND acct_id = q_acct.id AND owner_id = {$this->user->getUserId()};";
		return $this->DB->query($sql);
	}

	function updateAccount($name,$acctId) {
		$accountNameEscaped = Normalize::mysql($name);
		$sql = "UPDATE q_acct,q_owners SET name = '{$accountNameEscaped}' WHERE q_acct.id = {$acctId} AND acct_id = q_acct.id AND owner_id = {$this->user->getUserId()};";
		return $this->DB->query($sql);
	}

	function getAccountNameById($id) {
		$sql = "SELECT name FROM q_acct
        WHERE id = {$id};";
		$this->DB->query($sql);
		$return = $this->DB->fetch();
		return $return['name'];
	}

	function getOwnedAccounts() {
		$sql = "SELECT * FROM q_acct,q_owners
        WHERE q_acct.id = acct_id 
          AND owner_id = {$this->user->getUserId()}
          AND active = 1;";
		$this->ownedAccounts = $this->DB->query($sql);
	}

	function getSharedAccounts() {
		$sql = "SELECT * FROM q_acct,q_share,q_user_groups,q_owners
        WHERE q_share.acct_id=q_acct.id
          AND q_user_groups.group_id=q_share.group_id
          AND q_user_groups.user_id = {$this->user->getUserId()}
          AND q_share.active = 1
          AND q_acct.active = 1
          AND q_owners.acct_id = q_acct.id
          AND q_owners.owner_id <> {$this->user->getUserId()}
          GROUP BY q_acct.id;";
		$this->sharedAccounts = $this->DB->query($sql);
	}

	function buildWidget() {
		$this->getSharedAccounts();
		$this->getOwnedAccounts();
		$fsQuickAccounts = new HTMLFieldset($this->container);
		new HTMLLegend($fsQuickAccounts,'Account Management');
		$this->buildCreateAccountForm($fsQuickAccounts);
		$this->buildOwnedAccountsTable($fsQuickAccounts);
		$this->buildSharedAccountsTable($fsQuickAccounts);
		$this->printHTML();
	}

	function buildOwnedAccountsTable($parentElement) {
		if ($this->DB->num($this->ownedAccounts)>0) {
			$divOwnedAccounts = new HTMLDiv($parentElement,'',self::getOwnedAcctClass());
			$this->buildAccountsTable($divOwnedAccounts,'Owned Accounts:',$this->ownedAccounts,self::getOwnedAcctClass());
		}
	}

	function buildSharedAccountsTable($parentElement) {
		if ($this->DB->num($this->sharedAccounts)>0) {
			$divSharedAccounts = new HTMLDiv($parentElement,'',self::getSharedAcctClass());
			$this->buildAccountsTable($divSharedAccounts,'Shared Accounts:',$this->sharedAccounts,self::getSharedAcctClass(),false);
		}
	}

	function buildAccountsTable($parentElement,$title,$queryResult,$tableName,$editable=true) {
		new HTMLHeading($parentElement,5,$title);
		$cols = ($editable) ? 3 : 1;
		$tableListAccounts = new Table($parentElement,$this->DB->num($queryResult),$cols,$tableName);
		$i = 0;
		while ($account = $this->DB->fetch($queryResult)) {
			$accountName = (empty($account['name'])) ? $this->getAccountNameById($this->getEditAcctId()) : $account['name'];
			$inputId = $this->getEditAcctNameInId().'_'.$account['id'];
			$inputName = $this->getEditAcctNameInName().'_'.$account['id'];

			$inputEditAccount = new HTMLInputText($tableListAccounts->cells[$i][0],$inputName,$accountName,$inputId,$this->getEditAcctNameClass());
			if ($editable) {
				$jsEdit = "QaAccountEdit('{$this->parentId}','{$inputId}','{$account['id']}');";
				$aEditAccount = new HTMLAnchor($tableListAccounts->cells[$i][1],'#','Edit');
				$aEditAccount->setAttribute('onclick',$jsEdit);
				$aDropAccount = new HTMLAnchor($tableListAccounts->cells[$i][2],'#','Delete');
				$aDropAccount->setAttribute('onclick',"if(confirmSubmit('Are you sure you want to delete the \'".$account['name']."\' account?')) { QaAccountDrop('{$this->parentId}','{$account['id']}'); }");
			} else {
				$inputEditAccount->setAttribute('disabled',"disabled");
			}
			$i++;
		}
	}

	function buildCreateAccountForm($parentElement) {
		$divAddAccount = new HTMLDiv($parentElement,'',$this->getCreateAcctClass());
		new HTMLHeading($divAddAccount,5,'Add Account:');
		new HTMLInputText($divAddAccount,$this->getCreateAcctInName(),$this->acctName,$this->getCreateAcctInId(),$this->getCreateAcctClass());
		$aAddAccount = new HTMLAnchor($divAddAccount,'#','Add Account');
		$aAddAccount->setAttribute('onclick',"QaAccountAdd('{$this->parentId}','{$this->getCreateAcctInId()}');");
	}
}
?>