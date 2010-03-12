<?php
class ManageQuickAccountsWidget {
  private $focusId = '';
  private $infoMsg;
  private $parentElement;
  private $DB;
  private $siteInfo;
  private $user;
  private $ownedAccounts; // MySQL result
  private $sharedAccounts; // MySQL result

  private $createAcctName = '';
  private $dropAcctId = false;
  private $editAcctId = false;
  private $editAcctName = false;
  private $accountFound = false;
  private $containerId = false;

  const createAccDiv = 'create_account';
  const createAccForm = 'create_account';
  const createAccText = 'account_name';
  const createAccTextId = 'account_name_input';
  const createAccButton = 'account_button';

  const editAccNameDiv = 'edit_name';
  const editAccNameForm = 'edit_name';
  const editAccNameHidden = 'edit_account_id';
  const editAccNameText = 'new_account_name';
  const editAccNameTextId = 'new_account_name_input';
  const editAccNameButton = 'edit_button';

  const editAccForm = 'edit_account';
  const editAccHidden = 'edit_account';
  const editAccHiddenId = 'edit_account_input';
  const editAccButton = 'edit_account_submit';

  const dropAccForm = 'drop_account';
  const dropAccHidden = 'drop_account';
  const dropAccHiddenId = 'drop_account_input';
  const dropAccButton = 'drop_account_submit';

  const sharedAccDiv = 'shared_accounts';
  const sharedAccTable = 'shared_accounts';

  const ownedAccDiv = 'owned_accounts';
  const ownedAccTable = 'owned_accounts';

  function __construct($parentElement,$DB,$siteInfo,$infoMsg,$user) {
    $this->infoMsg = $infoMsg;
    $this->parentElement = $parentElement;
    $this->DB = $DB;
    $this->siteInfo = $siteInfo;
    $this->user = $user;
    $this->setFromPost();
    if ($this->checkAccountName($this->getCreateAcctName())) {
      $this->insertAccount();
      $this->insertOwner();
    }
    if ($this->getEditAcctId() and $this->getEditAcctName() and $this->checkAccountName($this->getEditAcctName())) {
      if ($this->updateAccount()) {
        $this->infoMsg->addMessage(2,'Account was successsfully updated.');
      }
    }
    if ($this->getDropAcctId()) {
      if ($this->dropAccount()) {
        $this->infoMsg->addMessage(2,'Account was successsfully deleted.');
      }
    }
    $this->getOwnedAccounts();
    $this->getSharedAccounts();
    $this->buildWidget();
  }

  function buildWidget() {
    $divQuickAccounts = new HTMLDiv($this->parentElement,'quick_accounts');
    $this->setContainerId($divQuickAccounts->getId());
    new HTMLHeading($divQuickAccounts,4,'Account Management');
    if ($this->getEditAcctId()) {
      $this->addEditAccountForm($divQuickAccounts);
    }
    $this->addCreateAccountForm($divQuickAccounts);
    $this->addOwnedAccountsTable($divQuickAccounts);
    $this->addSharedAccountsTable($divQuickAccounts);
    if (!($this->accountFound)) {
      $this->infoMsg->addMessage(3,'You don\'t belong to any accounts.  Add an account to get started.');
      $this->focusId = self::createAccTextId;
    }
  }

  function getFocusId() { return $this->focusId; }
  function getCreateAcctName() { return $this->createAcctName; }
  function getDropAcctId() { return $this->dropAcctId; }
  function getEditAcctId() { return $this->editAcctId; }
  function getEditAcctName() { return $this->editAcctName; }
  function getContainerId() { return $this->containerId; }

  function setCreateAcctName($name) { $this->createAcctName = $name; }
  function setDropAcctId($id) { $this->dropAcctId = $id; }
  function setEditAcctId($id) { $this->editAcctId = $id; }
  function setEditAcctName($id) { $this->editAcctName = $id; }
  function setContainerId($id) { $this->containerId = $id; }

  function setFromPost() {
    if(isset($_POST[self::editAccHidden])) { $this->setEditAcctId($_POST[self::editAccHidden]); }
    elseif(isset($_POST[self::editAccNameHidden])) { $this->setEditAcctId($_POST[self::editAccNameHidden]); }
    if(isset($_POST[self::createAccText])) { $this->setCreateAcctName($_POST[self::createAccText]); }
    if(isset($_POST[self::editAccNameText])) { $this->setEditAcctName($_POST[self::editAccNameText]); }
    if(isset($_POST[self::dropAccHidden])) { $this->setDropAcctId($_POST[self::dropAccHidden]); }
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


  function addOwnedAccountsTable($parentElement) {
    if ($this->DB->num($this->ownedAccounts)>0) {
      $divOwnedAccounts = new HTMLDiv($parentElement,self::ownedAccDiv);
      $this->accountFound = true;
      $this->addAccountsTable($divOwnedAccounts,'Owned Accounts:',$this->ownedAccounts,self::ownedAccTable);
    }
  }

  function addSharedAccountsTable($parentElement) {
    if ($this->DB->num($this->sharedAccounts)>0) {
      $divSharedAccounts = new HTMLDiv($parentElement,self::sharedAccDiv);
      $this->accountFound = true;
      $this->addAccountsTable($divSharedAccounts,'Shared Accounts:',$this->sharedAccounts,self::sharedAccTable);
    }
  }

  function addAccountsTable($parentElement,$title,$queryResult,$tableName) {
    new HTMLHeading($parentElement,5,$title);
    $tableListAccounts = new Table($parentElement,$this->DB->num($queryResult),3,$tableName);
    $i = 0;
    while ($account = $this->DB->fetch($queryResult)) {
      new HTMLText($tableListAccounts->cells[$i][0],$account['name']);

      $formEditAccount = new HTMLForm($tableListAccounts->cells[$i][1],$this->siteInfo->getSelfFileName(),self::editAccForm);
      new HTMLInputHidden($formEditAccount,self::editAccHidden,$account['acct_id']);
      new HTMLInputSubmit($formEditAccount,self::editAccButton,'Edit');

      $formDropAccount = new HTMLForm($tableListAccounts->cells[$i++][2],$this->siteInfo->getSelfFileName(),self::dropAccForm);
      new HTMLInputHidden($formDropAccount,self::dropAccHidden,$account['acct_id']);
      $inputDelete = new HTMLInputSubmit($formDropAccount,self::dropAccButton,'Delete');
      $inputDelete->setAttribute('onclick','return confirmSubmit("Are you sure you want to delete the \''.$account['name'].'\' account?")');
    }
  }

  function addEditAccountForm($parentElement) {
    $divEditAccount = new HTMLDiv($parentElement,self::editAccNameDiv);
    new HTMLHeading($divEditAccount,5,'Edit Account:');
    $formEditAccount = new HTMLForm($divEditAccount,$this->siteInfo->getSelfFileName(),self::editAccNameForm);
    new HTMLInputHidden($formEditAccount,self::editAccNameHidden,$this->getEditAcctId());
    $editAccountName = $this->getEditAcctName();
    $accountName = (empty($editAccountName)) ? $this->getAccountNameById($this->getEditAcctId()) : $this->getEditAcctName();
    new HTMLInputText($formEditAccount,self::editAccNameText,$accountName);
    new HTMLInputSubmit($formEditAccount,self::editAccNameButton,'Edit Account');
    $this->focusId = self::editAccNameTextId;
  }

  function addCreateAccountForm($parentElement) {
    $divAddAccount = new HTMLDiv($parentElement,self::createAccDiv);
    new HTMLHeading($divAddAccount,5,'Add Account:');
    $formAddAccount = new HTMLForm($divAddAccount,$this->siteInfo->getSelfFileName(),self::createAccForm);
    new HTMLInputText($formAddAccount,self::createAccText,$this->getCreateAcctName());
    new HTMLInputSubmit($formAddAccount,self::createAccButton,'Add Account');
  }
}
?>