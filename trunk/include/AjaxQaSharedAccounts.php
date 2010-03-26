<?php
class AjaxQaSharedAccounts extends AjaxQaWidget {
  private $ownedAccounts; // MySQL result
  private $sharedAccounts; // MySQL result
  private $parentId;

  const splitGroupAcct = 'split_grp_acct';
  const createSharedAcct = 'add_shared_acct';
  const editSharedAcctName = 'acct_name';
  const sharedAcct = 'shared_shared_acct';
  const ownedAcct = 'owned_shared_acct';

  function getSplitGroupAcctClass() { return self::splitGroupAcct; }
  function getCreateSharedAcctClass() { return self::createSharedAcct; }
  function getCreateSharedAcctInName() { return self::getCreateSharedAcctClass().'_name'; }
  function getCreateSharedAcctInId() { return self::getCreateSharedAcctClass().'_text'; }
  function getEditSharedAcctNameClass() { return self::editSharedAcctName; }
  function getEditSharedAcctNameInName() { return self::getEditSharedAcctNameClass().'_name'; }
  function getEditSharedAcctNameInId() { return self::getEditSharedAcctNameClass().'_text'; }
  function getSharedAcctClass() { return self::sharedAcct; }
  function getOwnedAcctClass() { return self::ownedAcct; }

  const createGrp = 'add_grp';
  const editGrpName = 'grp_name';
  const activeGrp = 'active_grps';
  const inactiveGrp = 'inactive_grps';

  function getCreateGrpClass() { return self::createGrp; }
  function getCreateGrpInName() { return self::getCreateGrpClass().'_name'; }
  function getCreateGrpInId() { return self::getCreateGrpClass().'_text'; }
  function getEditGrpNameClass() { return self::editGrpName; }
  function getEditGrpNameInName() { return self::getEditGrpNameClass().'_name'; }
  function getEditGrpNameInId() { return self::getEditGrpNameClass().'_text'; }
  function getActiveGrpClass() { return self::activeGrp; }
  function getInactiveGrpClass() { return self::inactiveGrp; }

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
          AND q_share.active = 1;";
    $this->sharedAccounts = $this->DB->query($sql);
  }

  function getActiveGroups() {
    $sql = "SELECT * FROM q_group,q_user_groups
        WHERE q_group.id = group_id 
          AND user_id = {$this->user->getUserId()}
          AND active = 1;";
    $this->activeGroups = $this->DB->query($sql);
  }

  function getInactiveGroups() {
    $sql = "SELECT * FROM q_group,q_user_groups
        WHERE q_group.id = group_id 
          AND user_id = {$this->user->getUserId()}
          AND active = 0;";
    $this->inactiveGroups = $this->DB->query($sql);
  }

  function buildWidget() {
    //$this->makeGroupsDroppable($this->container);
    $this->getActiveGroups();
    $this->getInactiveGroups();
    $this->getSharedAccounts();
    $this->getOwnedAccounts();
    $fsQuickAccounts = new HTMLFieldset($this->container);
    new HTMLLegend($fsQuickAccounts,'Account Sharing');
    $tableSplit = new Table($fsQuickAccounts,1,2,self::getSplitGroupAcctClass());
    //$this->buildCreateAccountForm($tableSplit->cells[0][0]);
    $this->buildOwnedAccountsTable($tableSplit->cells[0][0]);
    $this->buildSharedAccountsTable($tableSplit->cells[0][0]);
    $this->buildActiveGroupsTable($tableSplit->cells[0][1]);
    //$this->buildInactiveGroupsTable($tableSplit->cells[0][1]);
    $this->printHTML();
  }

  //$("p.grp_name").draggable({ revert: \'invalid\' });
  function makeGroupsDroppable($parentElement) {
    new HTMLScript($parentElement,'',$this->siteInfo->getSiteHTTP().$this->siteInfo->getJsDir().'/jquery-1.4.2.js',FALSE);
    new HTMLScript($parentElement,'',$this->siteInfo->getSiteHTTP().$this->siteInfo->getJsDir().'/ui/jquery.ui.core.js',FALSE);
    new HTMLScript($parentElement,'',$this->siteInfo->getSiteHTTP().$this->siteInfo->getJsDir().'/ui/jquery.ui.widget.js',FALSE);
    new HTMLScript($parentElement,'',$this->siteInfo->getSiteHTTP().$this->siteInfo->getJsDir().'/ui/jquery.ui.mouse.js',FALSE);
    new HTMLScript($parentElement,'',$this->siteInfo->getSiteHTTP().$this->siteInfo->getJsDir().'/ui/jquery.ui.draggable.js',FALSE);
    new HTMLScript($parentElement,'',$this->siteInfo->getSiteHTTP().$this->siteInfo->getJsDir().'/ui/jquery.ui.droppable.js',FALSE);

    $script ='
	$(function() {

		$("#active_grps_div").draggable({ revert: \'invalid\' });

		$("input.acct_name").droppable({
			activeClass: \'ui-state-hover\',
			hoverClass: \'ui-state-active\',
			drop: function(event, ui) {
				$(this).addClass(\'ui-state-highlight\').find(\'p\').html(\'Dropped!\');
			}
		});

	});';
    new HTMLScript($parentElement,$script,'',FALSE);
  }

  function buildOwnedAccountsTable($parentElement) {
    if ($this->DB->num($this->ownedAccounts)>0) {
      $divOwnedAccounts = new HTMLDiv($parentElement,self::getOwnedAcctClass());
      $this->buildAccountsTable($divOwnedAccounts,'Owned Accounts:',$this->ownedAccounts,self::getOwnedAcctClass(),false);
    }
  }

  function buildSharedAccountsTable($parentElement) {
    if ($this->DB->num($this->sharedAccounts)>0) {
      $divSharedAccounts = new HTMLDiv($parentElement,self::getSharedAcctClass());
      $this->buildAccountsTable($divSharedAccounts,'Shared Accounts:',$this->sharedAccounts,self::getSharedAcctClass(),false);
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

  function buildActiveGroupsTable($parentElement) {
    if ($this->DB->num($this->activeGroups)>0) {
      $divGroups = new HTMLDiv($parentElement,self::getActiveGrpClass());
      $this->buildGroupsTable($divGroups,'Active Groups:',$this->activeGroups,self::getActiveGrpClass(),false);
    }
  }

  function buildInactiveGroupsTable($parentElement) {
    if ($this->DB->num($this->inactiveGroups)>0) {
      $divGroups = new HTMLDiv($parentElement,self::getInactiveGrpClass());
      $this->buildGroupsTable($divGroups,'Inactive Groups:',$this->inactiveGroups,self::getInactiveGrpClass(),false);
    }
  }


  function buildGroupsTable($parentElement,$title,$queryResult,$tableName,$editable=true) {
    new HTMLHeading($parentElement,5,$title);
    $cols = ($editable) ? 3 : 2;
    $tableListGroups = new Table($parentElement,$this->DB->num($queryResult),$cols,$tableName);
    $i = 0;
    while ($group = $this->DB->fetch($queryResult)) {
      $groupName = (empty($group['name'])) ? $this->getGroupNameById($this->getEditGrpId()) : $group['name'];
      $inputId = $this->getEditGrpNameInId().'_'.$group['group_id'];
      $inputName = $this->getEditGrpNameInName().'_'.$group['group_id'];

      //$inputEditGroup = new HTMLInputText($tableListGroups->cells[$i][0],$inputName,$groupName,$this->getEditGrpNameClass(),$inputId);
      $inputEditGroup = new HTMLParagraph($tableListGroups->cells[$i][0],$groupName,$this->getEditGrpNameClass(),$inputId);
      if ($editable) {
        $aEditGroup = new HTMLAnchor($tableListGroups->cells[$i][1],'#','Edit');
        $aEditGroup->setAttribute('onclick',"QaGroupEdit('{$this->parentId}','{$inputId}','{$group['group_id']}');");
        $aDropGroup = new HTMLAnchor($tableListGroups->cells[$i][2],'#','Leave');
        $aDropGroup->setAttribute('onclick',"if(confirmSubmit('Are you sure you want to leave the \'".$group['name']."\' group?')) { QaGroupDrop('{$this->parentId}','{$group['group_id']}'); }");
      } else {
        //$inputEditGroup->setAttribute('disabled',"disabled");
        $inputEditGroup->setAttribute('style',"cursor:move;");
        //$aRejoinGroup = new HTMLAnchor($tableListGroups->cells[$i][1],'#','Join');
        //$aRejoinGroup->setAttribute('onclick',"QaGroupRejoin('{$this->parentId}','{$group['group_id']}');");
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