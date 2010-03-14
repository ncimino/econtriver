<?php
class AjaxQaAccounts extends AjaxQaWidget {
  private $ownedAccounts; // MySQL result
  private $sharedAccounts; // MySQL result
  private $parentId;

  const main = 'quick_accts';

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

  function __construct($parentId) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);

    parent::__construct();
    $this->parentId = $parentId;
    if (!$this->user->verifyUser()) {
      echo "User info is invalid, please login!";
    }
  }
  
  function addEntries($name) {
    if ($escapedName = $this->checkAccountName($name)) {
      if ($this->insertAccount($escapedName) and $this->insertOwner()) {
        $this->infoMsg->addMessage(2,'Account was successfully created.');
      }
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
      $divOwnedAccounts = new HTMLDiv($parentElement,self::ownedAccDiv);
      $this->accountFound = true;
      $this->buildAccountsTable($divOwnedAccounts,'Owned Accounts:',$this->ownedAccounts,self::ownedAccTable);
    }
  }

  function buildSharedAccountsTable($parentElement) {
    if ($this->DB->num($this->sharedAccounts)>0) {
      $divSharedAccounts = new HTMLDiv($parentElement,self::sharedAccDiv);
      $this->accountFound = true;
      $this->buildAccountsTable($divSharedAccounts,'Shared Accounts:',$this->sharedAccounts,self::sharedAccTable,false);
    }
  }

  function buildAccountsTable($parentElement,$title,$queryResult,$tableName,$editable=true) {
    new HTMLHeading($parentElement,5,$title);
    $cols = ($editable) ? 3 : 1;
    $tableListAccounts = new Table($parentElement,$this->DB->num($queryResult),$cols,$tableName);
    $i = 0;
    while ($account = $this->DB->fetch($queryResult)) {
      $accountName = (empty($account['name'])) ? $this->getAccountNameById($this->getEditAcctId()) : $account['name'];
      $inputAddAccount = new HTMLInputText($tableListAccounts->cells[$i][0],$this->getEditAcctNameInName(),$accountName,$this->getEditAcctNameClass(),$this->getEditAcctNameInId());
      $inputAddAccount->setAttribute('onclick',"QaAddAccount('quick_accounts_manage_div');");

      if ($editable) {
        $formEditAccount = new HTMLForm($tableListAccounts->cells[$i][1],$this->siteInfo->getSelfFileName(),self::editAcct);
        new HTMLInputHidden($formEditAccount,$this->getEditAcctHiddenName(),$account['acct_id']);
        new HTMLInputSubmit($formEditAccount,$this->getEditAcctClass(),'Edit');

        $formDropAccount = new HTMLForm($tableListAccounts->cells[$i++][2],$this->siteInfo->getSelfFileName(),self::dropAccForm);
        new HTMLInputHidden($formDropAccount,self::dropAccHidden,$account['acct_id']);
        $inputDelete = new HTMLInputSubmit($formDropAccount,self::dropAccButton,'Delete');
        $inputDelete->setAttribute('onclick','return confirmSubmit("Are you sure you want to delete the \''.$account['name'].'\' account?")');
      }
    }
  }

  function buildCreateAccountForm($parentElement) {
    $divAddAccount = new HTMLDiv($parentElement,$this->getCreateAcctClass());
    new HTMLHeading($divAddAccount,5,'Add Account:');
    $formAddAccount = new HTMLForm($divAddAccount,$this->siteInfo->getSelfFileName(),$this->getCreateAcctClass());
    new HTMLInputText($formAddAccount,$this->getCreateAcctInName(),'',$this->getCreateAcctClass(),$this->getCreateAcctInId());
    $aAddAccount = new HTMLAnchor($formAddAccount,'#','Add Account');
    $aAddAccount->setAttribute('onclick',"QaAddAccount('{$this->parentId}','{$this->getCreateAcctInId()}');");
  }
}
?>