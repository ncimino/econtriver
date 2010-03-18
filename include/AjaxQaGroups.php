<?php
class AjaxQaGroups extends AjaxQaWidget {
  private $ownedGroups; // MySQL result
  private $sharedGroups; // MySQL result
  private $parentId;

  const createGrp = 'add_grp';
  const editGrpName = 'edit_name';
  const sharedGrp = 'shared_grps';
  const ownedGrp = 'owned_grps';

  function getCreateGrpClass() { return self::createGrp; }
  function getCreateGrpInName() { return self::getCreateGrpClass().'_name'; }
  function getCreateGrpInId() { return self::getCreateGrpClass().'_text'; }
  function getEditGrpNameClass() { return self::editGrpName; }
  function getEditGrpNameInName() { return self::getEditGrpNameClass().'_name'; }
  function getEditGrpNameInId() { return self::getEditGrpNameClass().'_text'; }
  function getSharedGrpClass() { return self::sharedGrp; }
  function getOwnedGrpClass() { return self::ownedGrp; }

  function __construct($parentId) {
    parent::__construct();
    $this->parentId = $parentId;
    if (!$this->user->verifyUser()) {
      $this->infoMsg->addMessage(0,'User info is invalid, please login first.');
    }
  }

  function addEntries($name) {
    if ($escapedName = $this->checkGroupName($name)) {
      if ($this->insertGroup($escapedName) and $this->insertUserGroup()) {
        $this->infoMsg->addMessage(2,'Group was successfully created.');
      }
    }
  }

  function updateEntries($name,$grpId) {
    if (!empty($grpId) and $sanitizedName = $this->checkGroupName($name)) {
      if ($this->updateGroup($sanitizedName,$grpId)) {
        $this->infoMsg->addMessage(2,'Group was successfully updated.');
      }
    }
  }

  function dropEntries($grpId) {
    if (!empty($grpId) and $this->dropGroup($grpId)) {
      $this->infoMsg->addMessage(2,'Group was successfully deleted.');
    }
  }

  function checkGroupName($name) {
    if (!($sanitizedName = Normalize::sanitize($name,$this->infoMsg,$this->siteInfo))) {
      return false;
    } elseif (!(Normalize::accountNames($sanitizedName,$this->infoMsg))) {
      return false;
    } else {
      return $sanitizedName;
    }
  }

  function insertGroup($grpName) {
    $grpNameEscaped = Normalize::mysql($grpName);
    $sql = "INSERT INTO q_group (name)
VALUES ('{$grpNameEscaped}');";
    return $this->DB->query($sql);
  }

  function insertUserGroup() {
    $sql = "INSERT INTO q_user_groups (group_id,user_id)
VALUES ({$this->DB->lastID()},{$this->user->getUserId()});";
    return $this->DB->query($sql);
  }

  function dropGroup($userId) {
    $sql = "UPDATE q_user_groups SET active = 0 WHERE user_id = {$userId}; AND group_id = ";
    return $this->DB->query($sql);
  }

  function updateGroup($name,$id) {
    $accountNameEscaped = Normalize::mysql($name);
    $sql = "UPDATE q_group SET name = '{$accountNameEscaped}' WHERE id = {$id};";
    return $this->DB->query($sql);
  }

  function getGroupNameById($id) {
    $sql = "SELECT name FROM q_group
        WHERE id = {$id};";
    $this->DB->query($sql);
    $return = $this->DB->fetch();
    return $return['name'];
  }

  function getOwnedGroups() {
    $sql = "SELECT * FROM q_group,q_owners
        WHERE q_group.id = group_id 
          AND owner_id = {$this->user->getUserId()}
          AND active = 1;";
    $this->ownedGroups = $this->DB->query($sql);
  }

  function getSharedGroups() {
    $sql = "SELECT * FROM q_group,q_share,q_user_groups
        WHERE q_share.group_id=q_group.id
          AND q_user_groups.group_id=q_share.group_id
          AND q_user_groups.user_id = {$this->user->getUserId()}
          AND active = 1;";
    $this->sharedGroups = $this->DB->query($sql);
  }

  function buildWidget() {
    $this->getSharedGroups();
    $this->getOwnedGroups();
    $fsQuickGroups = new HTMLFieldset($this->container);
    new HTMLLegend($fsQuickGroups,'Group Management');
    $this->buildCreateGroupForm($fsQuickGroups);
    $this->buildOwnedGroupsTable($fsQuickGroups);
    $this->buildSharedGroupsTable($fsQuickGroups);
    $this->printHTML();
  }

  function buildOwnedGroupsTable($parentElement) {
    if ($this->DB->num($this->ownedGroups)>0) {
      $divOwnedGroups = new HTMLDiv($parentElement,self::getOwnedGrpClass());
      $this->buildGroupsTable($divOwnedGroups,'Owned Groups:',$this->ownedGroups,self::getOwnedGrpClass());
    }
  }

  function buildSharedGroupsTable($parentElement) {
    if ($this->DB->num($this->sharedGroups)>0) {
      $divSharedGroups = new HTMLDiv($parentElement,self::getSharedGrpClass());
      $this->buildGroupsTable($divSharedGroups,'Shared Groups:',$this->sharedGroups,self::getSharedGrpClass(),false);
    }
  }

  function buildGroupsTable($parentElement,$title,$queryResult,$tableName,$editable=true) {
    new HTMLHeading($parentElement,5,$title);
    $cols = ($editable) ? 3 : 1;
    $tableListGroups = new Table($parentElement,$this->DB->num($queryResult),$cols,$tableName);
    $i = 0;
    while ($account = $this->DB->fetch($queryResult)) {
      $accountName = (empty($account['name'])) ? $this->getGroupNameById($this->getEditGrpId()) : $account['name'];
      $inputId = $this->getEditGrpNameInId().'_'.$account['id'];
      $inputName = $this->getEditGrpNameInName().'_'.$account['id'];

      $inputEditGroup = new HTMLInputText($tableListGroups->cells[$i][0],$inputName,$accountName,$this->getEditGrpNameClass(),$inputId);
      if ($editable) {
        $jsEdit = "QaEditGroup('{$this->parentId}','{$inputId}','{$account['id']}');";
        $inputEditGroup->setAttribute('onkeyup',$jsEdit);
        $aEditGroup = new HTMLAnchor($tableListGroups->cells[$i][1],'#','Edit');
        $aEditGroup->setAttribute('onclick',$jsEdit);
        $aDropGroup = new HTMLAnchor($tableListGroups->cells[$i][2],'#','Delete');
        $aDropGroup->setAttribute('onclick',"if(confirmSubmit('Are you sure you want to delete the \'".$account['name']."\' account?')) { QaDropGroup('{$this->parentId}','{$account['id']}'); }");
      } else {
        $inputEditGroup->setAttribute('disabled',"disabled");
      }
      $i++;
    }
  }

  function buildCreateGroupForm($parentElement) {
    $divAddGroup = new HTMLDiv($parentElement,$this->getCreateGrpClass());
    new HTMLHeading($divAddGroup,5,'Add Group:');
    new HTMLInputText($divAddGroup,$this->getCreateGrpInName(),'',$this->getCreateGrpClass(),$this->getCreateGrpInId());
    $aAddGroup = new HTMLAnchor($divAddGroup,'#','Add Group');
    $aAddGroup->setAttribute('onclick',"QaAddGroup('{$this->parentId}','{$this->getCreateGrpInId()}');");
  }
}
?>