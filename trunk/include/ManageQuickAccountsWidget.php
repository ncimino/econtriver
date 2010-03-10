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
  private $accountName = '';

  const createAccDiv = 'create_account';
  const createAccForm = 'create_account';
  const createAccText = 'account_name';
  const createAccTextId = 'account_name_input';
  const createAccButton = 'account_button';

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
    $this->setAccountName();

    if ($this->checkAccountName()) {
      $this->insertAccount();
      $this->insertOwner();
    }
    $this->updateDB();
    $this->getOwnedAccounts();
    $this->getSharedAccounts();
    $this->buildWidget();
  }

  function setAccountName($name = NULL) {
    if(empty($name)) { $name = $_POST[self::createAccText]; }
    $this->accountName = $name;
  }

  function checkAccountName($name = NULL) {
    if (empty($name)) { $name = $this->accountName; }
    if (!($sanitizedName = Normalize::sanitize($name,$this->infoMsg,$this->siteInfo))) {
      $this->focusId = self::createAccTextId;
      return false;
    } elseif (!(Normalize::accountNames($sanitizedName,$this->infoMsg))) {
      $this->focusId = self::createAccTextId;
      return false;
    } else {
      return true;
    }
  }

  function insertAccount() {
    $accountNameEscaped = Normalize::mysql($this->accountName);
    $sql = "INSERT INTO q_acct (name)
VALUES ('{$accountNameEscaped}');";
    if (!($this->DB->query($sql))) {
      $this->infoMsg->addMessage(-1,'An error occurred while creating account: '.$this->accountName);
    }
  }

  function insertOwner() {
    $sql = "INSERT INTO q_owners (acct_id,owner_id)
VALUES ({$this->DB->lastID()},{$this->user->getUserId()});";
    if (!($this->DB->query($sql))) {
      $this->infoMsg->addMessage(-1,'An error occurred while making you the owner of the new account: '.$accountName);
    }
  }

  function updateDB() {

  }

  function getOwnedAccounts() {
    $sql = "SELECT * FROM q_acct,q_owners
    		WHERE q_acct.id = acct_id 
    		  AND owner_id = {$this->user->getUserId()};";
    if (!($this->ownedAccounts = $this->DB->query($sql))) {
      $this->infoMsg->addMessage(-1,'There was a problem retrieving the Quick Accounts that you own.');
    }
  }

  function getSharedAccounts() {
    $sql = "SELECT * FROM q_acct,q_share,q_user_groups
    		WHERE q_share.acct_id=q_acct.id
    		  AND q_user_groups.group_id=q_share.group_id
    		  AND q_user_groups.user_id = {$this->user->getUserId()};";
    if (!($this->sharedAccounts = $this->DB->query($sql))) {
      $this->infoMsg->addMessage(-1,'There was a problem retrieving the Quick Accounts that you own.');
    }
  }

  function buildWidget() {
    $this->addCreateAccountForm();
    $this->addOwnedAccountsTable();
    $this->addSharedAccountsTable();
  }

  function addOwnedAccountsTable() {
    $divOwnedAccounts = new HTMLDiv($this->parentElement,self::ownedAccDiv);
    if ($this->DB->num($this->ownedAccounts)==0) {
      $this->infoMsg->addMessage(2,'You don\'t own any accounts.  Add an account to get started.');
      $this->focusId = self::createAccTextId;
    } else {
      $tableListAccounts = new Table($divOwnedAccounts,$this->DB->num($this->ownedAccounts),2,self::ownedAccTable);
      $i = 0;
      while ($account = $this->DB->fetch($this->ownedAccounts)) {
        new HTMLText($tableListAccounts->cells[$i][0],$i.': ');
        new HTMLText($tableListAccounts->cells[$i++][1],$account['name']);
      }
    }
  }

  function addSharedAccountsTable() {
    $divSharedAccounts = new HTMLDiv($this->parentElement,self::sharedAccDiv);
    if ($this->DB->num($this->sharedAccounts)==0) {
      $this->infoMsg->addMessage(2,'You don\'t have any shared accounts setup.  Add an account to get started.');
      $this->focusId = self::createAccTextId;
    } else {
      $tableListAccounts = new Table($divSharedAccounts,$this->DB->num($this->sharedAccounts),2,self::sharedAccTable);
      $i = 0;
      while ($account = $this->DB->fetch($this->sharedAccounts)) {
        new HTMLText($tableListAccounts->cells[$i][0],$i.': ');
        new HTMLText($tableListAccounts->cells[$i++][1],$account['name']);
      }
    }
  }

  function addCreateAccountForm() {
    $divAddAccount = new HTMLDiv($this->parentElement,self::createAccDiv);
    $formAddAccount = new HTMLForm($divAddAccount,$this->siteInfo->getSelfFileName(),self::createAccForm);
    new HTMLInputText($formAddAccount,self::createAccText);
    //new HTMLInputButton($formAddAccount,self::createAccButton,'Add Account');
    new HTMLInputSubmit($formAddAccount,self::createAccButton,'Add Account');
  }
}
?>