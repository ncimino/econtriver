<?php
class ManageQuickAccountsWidget extends Widget {
  // Stores Get Data
  private $ownedAccounts; // MySQL result
  private $sharedAccounts; // MySQL result

  private $createAcctName = false;
  private $dropAcctId = false;
  private $editAcctId = false;
  private $editAcctName = false;

  private $displayStatus = 'none';
  private $accountFound = false;

  const createAcct = 'create_acct';
  const editAcctName = 'edit_acct_name';
  const editAcct = 'edit_acct';
  const dropAcct = 'drop_acct';
  const sharedAcct = 'shared_accounts';
  const ownedAcct = 'owned_accounts';

  const createAcct = 'add_acct';
  function getCreateAcctClass() { return self::createAcct; }
  function getCreateAcctInName() { return self::getCreateAcctClass().'_name'; }
  function getCreateAcctInId() { return self::getCreateAcctClass().'_text'; }

  const editAccName = 'edit_name';
  function getEditAcctNameClass() { return self::editAccName; }
  function getEditAcctNameInName() { return self::getEditAcctNameClass().'_name'; }
  function getEditAcctNameInId() { return self::getEditAcctNameClass().'_text'; }
  function getEditAcctNameHiddenName() { return self::getEditAcctNameClass().'_hidden'; }

  const editAcct = 'edit_account';
  function getEditAcctClass() { return self::editAcct; }
  function getEditAcctHiddenName() { return self::getEditAcctClass().'_hidden'; }

  const dropAccForm = 'drop_account';
  const dropAccHidden = 'drop_account';
  const dropAccHiddenId = 'drop_account_input';
  const dropAccButton = 'drop_account_submit';

  const sharedAccDiv = 'shared_accts';
  const sharedAccTable = 'shared_accts';

  const ownedAccDiv = 'owned_accts';
  const ownedAccTable = 'owned_accts';

  function __construct($parentElement,$DB,$siteInfo,$infoMsg,$user) {
    parent::__construct($parentElement,$DB,$siteInfo,$infoMsg,$user);
    $this->setFromPost();
    $this->addEntries();
    $this->updateEntries();
    $this->deleteEntries();
    $this->getEntries();
    $this->buildWidget();
  }

  function buildWidget() {
    $divQuickAccounts = new HTMLDiv($this->parentElement,self::main);
    $this->setContainerId($divQuickAccounts->getId());

    new HTMLHeading($divQuickAccounts,4,'Account Management');
    if ($this->getEditAcctId()) {
      $this->buildEditAccountForm($divQuickAccounts);
    } else {
      $this->buildCreateAccountForm($divQuickAccounts);
    }
    $this->buildOwnedAccountsTable($divQuickAccounts);
    $this->buildSharedAccountsTable($divQuickAccounts);
    if (!($this->accountFound)) {
      $this->infoMsg->addMessage(3,'You don\'t belong to any accounts.  Add an account to get started.');
      $this->displayStatus = 'block';
      $this->setFocusId($this->getCreateAcctInId());
    }
    $divQuickAccounts->setAttribute('style','display:'.$this->displayStatus);
  }

  function getCreateAcctName() { return $this->createAcctName; }
  function getDropAcctId() { return $this->dropAcctId; }
  function getEditAcctId() { return $this->editAcctId; }
  function getEditAcctName() { return $this->editAcctName; }

  function setCreateAcctName($name) { $this->createAcctName = $name; }
  function setDropAcctId($id) { $this->dropAcctId = $id; }
  function setEditAcctId($id) { $this->editAcctId = $id; }
  function setEditAcctName($id) { $this->editAcctName = $id; }

  function setFromPost() {
    if(isset($_POST[$this->getEditAcctHiddenName()])) { $this->setEditAcctId($_POST[$this->getEditAcctHiddenName()]); }
    elseif(isset($_POST[$this->getEditAcctNameHiddenName()])) { $this->setEditAcctId($_POST[$this->getEditAcctNameHiddenName()]); }
    if(isset($_POST[$this->getCreateAcctInName()])) { $this->setCreateAcctName($_POST[$this->getCreateAcctInName()]); }
    if(isset($_POST[$this->getEditAcctNameInName()])) { $this->setEditAcctName($_POST[$this->getEditAcctNameInName()]); }
    if(isset($_POST[self::dropAccHidden])) { $this->setDropAcctId($_POST[self::dropAccHidden]); }
  }

  function addEntries() {
    if ($this->checkAccountName($this->getCreateAcctName())) {
      if ($this->insertAccount() and $this->insertOwner()) {
        $this->infoMsg->addMessage(2,'Account was successfully created.');
        $this->setCreateAcctName('');
        $this->displayStatus = 'block';
      }
    } elseif ($this->getCreateAcctName()) {
      $this->displayStatus = 'block';
      $this->setFocusId($this->getCreateAcctInId());
    }
  }

  function updateEntries() {
    if ($this->getEditAcctId() and $this->getEditAcctName() and $this->checkAccountName($this->getEditAcctName())) {
      if ($this->updateAccount()) {
        $this->infoMsg->addMessage(2,'Account was successfully updated.');
        $this->displayStatus = 'block';
      }
    } elseif ($this->getEditAcctId() or $this->getEditAcctName()) {
      $this->displayStatus = 'block';
      $this->setFocusId($this->getEditAcctNameInId());
    }
  }

  function deleteEntries() {
    if ($this->getDropAcctId()) {
      if ($this->dropAccount()) {
        $this->infoMsg->addMessage(2,'Account was successfully deleted.');
        $this->displayStatus = 'block';
      }
    }
  }

  function getEntries() {
    $this->getOwnedAccounts();
    $this->getSharedAccounts();
  }

  function checkAccountName($name) {
    if (!($sanitizedName = Normalize::sanitize($name,$this->infoMsg,$this->siteInfo))) {
      return false;
    } elseif (!(Normalize::accountNames($sanitizedName,$this->infoMsg))) {
      return false;
    } else {
      return true;
    }
  }

  function insertAccount() {
    $accountNameEscaped = Normalize::mysql($this->getCreateAcctName());
    $sql = "INSERT INTO q_acct (name,active)
VALUES ('{$accountNameEscaped}',1);";
    return $this->DB->query($sql);
  }

  function insertOwner() {
    $sql = "INSERT INTO q_owners (acct_id,owner_id)
VALUES ({$this->DB->lastID()},{$this->user->getUserId()});";
    return $this->DB->query($sql);
  }

  function dropAccount() {
    $sql = "UPDATE q_acct SET active = 0 WHERE id = {$this->getDropAcctId()};";
    return $this->DB->query($sql);
  }

  function updateAccount() {
    $accountNameEscaped = Normalize::mysql($this->getEditAcctName());
    $sql = "UPDATE q_acct SET name = '{$accountNameEscaped}' WHERE id = {$this->getEditAcctId()};";
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
    $sql = "SELECT * FROM q_acct,q_share,q_user_groups
    		WHERE q_share.acct_id=q_acct.id
    		  AND q_user_groups.group_id=q_share.group_id
    		  AND q_user_groups.user_id = {$this->user->getUserId()}
    		  AND active = 1;";
    $this->sharedAccounts = $this->DB->query($sql);
  }


  function buildOwnedAccountsTable($parentElement) {
    if ($this->DB->num($this->ownedAccounts)>0) {
      $divOwnedAccounts = new HTMLDiv($parentElement,self::ownedAccDiv);
      $this->accountFound = true;
      $this->buildAccountsTable($divOwnedAccounts,'Owned Accounts:',$this->ownedAccounts,self::ownedAccTable);
    }
  }

  function buildSharedAccountsTable($parentElement) {
    if ($this->DB->num($this->sharedAccounts)>0) {
      $divSharedAccounts = new HTMLDiv($parentElement,self::sharedAccDiv);
      $this->accountFound = true;
      $this->buildAccountsTable($divSharedAccounts,'Shared Accounts:',$this->sharedAccounts,self::sharedAccTable);
    }
  }

  function buildAccountsTable($parentElement,$title,$queryResult,$tableName) {
    new HTMLHeading($parentElement,5,$title);
    $tableListAccounts = new Table($parentElement,$this->DB->num($queryResult),3,$tableName);
    $i = 0;
    while ($account = $this->DB->fetch($queryResult)) {
      new HTMLText($tableListAccounts->cells[$i][0],$account['name']);

      $formEditAccount = new HTMLForm($tableListAccounts->cells[$i][1],$this->siteInfo->getSelfFileName(),self::editAcct);
      new HTMLInputHidden($formEditAccount,$this->getEditAcctHiddenName(),$account['acct_id']);
      new HTMLInputSubmit($formEditAccount,$this->getEditAcctClass(),'Edit');

      $formDropAccount = new HTMLForm($tableListAccounts->cells[$i++][2],$this->siteInfo->getSelfFileName(),self::dropAccForm);
      new HTMLInputHidden($formDropAccount,self::dropAccHidden,$account['acct_id']);
      $inputDelete = new HTMLInputSubmit($formDropAccount,self::dropAccButton,'Delete');
      $inputDelete->setAttribute('onclick','return confirmSubmit("Are you sure you want to delete the \''.$account['name'].'\' account?")');
    }
  }

  function buildEditAccountForm($parentElement) {
    $fsEditAccount = new HTMLFieldset($parentElement,$this->getEditAcctNameClass());
    new HTMLLegend($fsEditAccount,'Edit Account');
    $formEditAccount = new HTMLForm($fsEditAccount,$this->siteInfo->getSelfFileName(),$this->getEditAcctNameClass());
    new HTMLInputHidden($formEditAccount,$this->getEditAcctNameHiddenName(),$this->getEditAcctId());
    $editAccountName = $this->getEditAcctName();
    $accountName = (empty($editAccountName)) ? $this->getAccountNameById($this->getEditAcctId()) : $this->getEditAcctName();
    new HTMLInputText($formEditAccount,$this->getEditAcctNameInName(),$accountName,$this->getEditAcctNameClass(),$this->getEditAcctNameInId());
    new HTMLInputSubmit($formEditAccount,$this->getEditAcctNameClass(),'Edit Account');
    $this->focusId = $this->getEditAcctNameInId();
  }

  function buildCreateAccountForm($parentElement) {
    $divAddAccount = new HTMLDiv($parentElement,$this->getCreateAcctClass());
    new HTMLHeading($divAddAccount,5,'Add Account:');
    $formAddAccount = new HTMLForm($divAddAccount,$this->siteInfo->getSelfFileName(),$this->getCreateAcctClass());
    new HTMLInputText($formAddAccount,$this->getCreateAcctInName(),$this->getCreateAcctName(),$this->getCreateAcctClass(),$this->getCreateAcctInId());
    new HTMLInputSubmit($formAddAccount,$this->getCreateAcctClass(),'Add Account');
  }
}
?>