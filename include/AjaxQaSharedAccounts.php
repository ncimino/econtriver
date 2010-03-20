<?php
class AjaxQaSharedAccounts extends AjaxQaWidget {
  private $ownedAccounts; // MySQL result
  private $sharedAccounts; // MySQL result
  private $parentId;

  const createSharedAcct = 'add_shared_acct';
  const editSharedAcctName = 'edit_name';
  const sharedAcct = 'shared_shared_acct';
  const ownedAcct = 'owned_shared_acct';

  function getCreateSharedAcctClass() { return self::createSharedAcct; }
  function getCreateSharedAcctInName() { return self::getCreateSharedAcctClass().'_name'; }
  function getCreateSharedAcctInId() { return self::getCreateSharedAcctClass().'_text'; }
  function getEditSharedAcctNameClass() { return self::editSharedAcctName; }
  function getEditSharedAcctNameInName() { return self::getEditSharedAcctNameClass().'_name'; }
  function getEditSharedAcctNameInId() { return self::getEditSharedAcctNameClass().'_text'; }
  function getSharedAcctClass() { return self::sharedAcct; }
  function getOwnedAcctClass() { return self::ownedAcct; }

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
    }
  }

  function updateEntries($name,$SharedAcctId) {
    if (!empty($SharedAcctId) and $sanitizedName = $this->checkAccountName($name)) {
      if ($this->updateAccount($sanitizedName,$SharedAcctId)) {
        $this->infoMsg->addMessage(2,'Account was successfully updated.');
      }
    }
  }

  function dropEntries($SharedAcctId) {
    if (!empty($SharedAcctId) and $this->dropAccount($SharedAcctId)) {
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

  function insertAccount($SharedAcctName) {
    $accountNameEscaped = Normalize::mysql($SharedAcctName);
    $sql = "INSERT INTO q_acct (name,active)
VALUES ('{$accountNameEscaped}',1);";
    return $this->DB->query($sql);
  }

  function insertOwner() {
    $sql = "INSERT INTO q_owners (acct_id,owner_id)
VALUES ({$this->DB->lastID()},{$this->user->getUserId()});";
    return $this->DB->query($sql);
  }

  function dropAccount($SharedAcctId) {
    $sql = "UPDATE q_share,q_owners SET active = 0 WHERE q_share.id = {$SharedAcctId} AND acct_id = q_share.id AND owner_id = {$this->user->getUserId()};";
    return $this->DB->query($sql);
  }

  function updateAccount($name,$SharedAcctId) {
    $accountNameEscaped = Normalize::mysql($name);
    $sql = "UPDATE q_share,q_owners SET name = '{$accountNameEscaped}' WHERE q_share.id = {$SharedAcctId} AND acct_id = q_share.id AND owner_id = {$this->user->getUserId()};";
    return $this->DB->query($sql);
  }

  function getAccountNameById($id) {
    $sql = "SELECT name FROM q_share
        WHERE id = {$id};";
    $this->DB->query($sql);
    $return = $this->DB->fetch();
    return $return['name'];
  }

  function getOwnedAccounts() {
    $sql = "SELECT * FROM q_acct,q_owners
        WHERE q_share.id = acct_id 
          AND owner_id = {$this->user->getUserId()}
          AND active = 1;";
    $this->ownedAccounts = $this->DB->query($sql);
  }

  function getSharedAccounts() {
    $sql = "SELECT * FROM q_acct,q_share
        WHERE acct_id=q_share.id
          AND q_user_groups.group_id=q_share.group_id
          AND q_user_groups.user_id = {$this->user->getUserId()}
          AND q_share.active = 1;";
    $this->sharedAccounts = $this->DB->query($sql);
  }

  function buildWidget() {
    $this->getSharedAccounts();
    $this->getOwnedAccounts();
    $fsQuickAccounts = new HTMLFieldset($this->container);
    new HTMLLegend($fsQuickAccounts,'Account Sharing');
    $this->buildCreateAccountForm($fsQuickAccounts);
    $this->buildOwnedAccountsTable($fsQuickAccounts);
    $this->buildSharedAccountsTable($fsQuickAccounts);
    $this->printHTML();
  }

  function buildOwnedAccountsTable($parentElement) {
    if ($this->DB->num($this->ownedAccounts)>0) {
      $divOwnedAccounts = new HTMLDiv($parentElement,self::getOwnedSharedAcctClass());
      $this->buildAccountsTable($divOwnedAccounts,'Owned Accounts:',$this->ownedAccounts,self::getOwnedSharedAcctClass());
    }
  }

  function buildSharedAccountsTable($parentElement) {
    if ($this->DB->num($this->sharedAccounts)>0) {
      $divSharedAccounts = new HTMLDiv($parentElement,self::getSharedSharedAcctClass());
      $this->buildAccountsTable($divSharedAccounts,'Shared Accounts:',$this->sharedAccounts,self::getSharedSharedAcctClass(),false);
    }
  }

  function buildAccountsTable($parentElement,$title,$queryResult,$tableName,$editable=true) {
    new HTMLHeading($parentElement,5,$title);
    $cols = ($editable) ? 3 : 1;
    $tableListAccounts = new Table($parentElement,$this->DB->num($queryResult),$cols,$tableName);
    $i = 0;
    while ($account = $this->DB->fetch($queryResult)) {
      $accountName = (empty($account['name'])) ? $this->getAccountNameById($this->getEditSharedAcctId()) : $account['name'];
      $inputId = $this->getEditSharedAcctNameInId().'_'.$account['id'];
      $inputName = $this->getEditSharedAcctNameInName().'_'.$account['id'];

      $inputEditAccount = new HTMLInputText($tableListAccounts->cells[$i][0],$inputName,$accountName,$this->getEditSharedAcctNameClass(),$inputId);
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
    $divAddAccount = new HTMLDiv($parentElement,$this->getCreateSharedAcctClass());
    new HTMLHeading($divAddAccount,5,'Add Account:');
    new HTMLInputText($divAddAccount,$this->getCreateSharedAcctInName(),'',$this->getCreateSharedAcctClass(),$this->getCreateSharedAcctInId());
    $aAddAccount = new HTMLAnchor($divAddAccount,'#','Add Account');
    $aAddAccount->setAttribute('onclick',"QaAccountAdd('{$this->parentId}','{$this->getCreateSharedAcctInId()}');");
  }
}
?>