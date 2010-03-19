<?php
class AjaxQaGroups extends AjaxQaWidget {
  private $activeGroups; // MySQL result
  private $inactiveGroups; // MySQL result
  private $parentId;

  const createGrp = 'add_grp';
  const editGrpName = 'edit_name';
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
      $this->infoMsg->addMessage(2,'Successfully left group.');
    }
  }
  
  function rejoinEntries($grpId) {
    if (!empty($grpId) and $this->rejoinGroup($grpId)) {
      $this->infoMsg->addMessage(2,'Successfully joined group.');
    }
  }

  function checkGroupName($name) {
    if (!($sanitizedName = Normalize::sanitize($name,$this->infoMsg,$this->siteInfo))) {
      return false;
    } elseif (!(Normalize::groupNames($sanitizedName,$this->infoMsg))) {
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
    $sql = "INSERT INTO q_user_groups (group_id,user_id,active)
VALUES ({$this->DB->lastID()},{$this->user->getUserId()},1);";
    return $this->DB->query($sql);
  }

  function dropGroup($group_id) {
    $sql = "UPDATE q_user_groups SET active = 0 WHERE user_id = {$this->user->getUserId()} AND group_id = {$group_id}";
    return $this->DB->query($sql);
  }

  function rejoinGroup($group_id) {
    $sql = "UPDATE q_user_groups SET active = 1 WHERE user_id = {$this->user->getUserId()} AND group_id = {$group_id}";
    return $this->DB->query($sql);
  }
  
  function updateGroup($name,$id) {
    $groupNameEscaped = Normalize::mysql($name);
    $sql = "UPDATE q_group SET name = '{$groupNameEscaped}' WHERE id = {$id};";
    return $this->DB->query($sql);
  }

  function getGroupNameById($id) {
    $sql = "SELECT name FROM q_group
        WHERE id = {$id};";
    $this->DB->query($sql);
    $return = $this->DB->fetch();
    return $return['name'];
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
    $this->getActiveGroups();
    $this->getInactiveGroups();
    $fsQuickGroups = new HTMLFieldset($this->container);
    new HTMLLegend($fsQuickGroups,'Group Management');
    $this->buildCreateGroupForm($fsQuickGroups);
    $this->buildActiveGroupsTable($fsQuickGroups);
    $this->buildInactiveGroupsTable($fsQuickGroups);
    $this->printHTML();
  }

  function buildActiveGroupsTable($parentElement) {
    if ($this->DB->num($this->activeGroups)>0) {
      $divGroups = new HTMLDiv($parentElement,self::getActiveGrpClass());
      $this->buildGroupsTable($divGroups,'Active Groups:',$this->activeGroups,self::getActiveGrpClass());
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

      $inputEditGroup = new HTMLInputText($tableListGroups->cells[$i][0],$inputName,$groupName,$this->getEditGrpNameClass(),$inputId);
      if ($editable) {
        $aEditGroup = new HTMLAnchor($tableListGroups->cells[$i][1],'#','Edit');
        $aEditGroup->setAttribute('onclick',"QaEditGroup('{$this->parentId}','{$inputId}','{$group['group_id']}');");
        $aDropGroup = new HTMLAnchor($tableListGroups->cells[$i][2],'#','Leave');
        $aDropGroup->setAttribute('onclick',"if(confirmSubmit('Are you sure you want to leave the \'".$group['name']."\' group?')) { QaDropGroup('{$this->parentId}','{$group['group_id']}'); }");
      } else {
        $inputEditGroup->setAttribute('disabled',"disabled");
        $aRejoinGroup = new HTMLAnchor($tableListGroups->cells[$i][1],'#','Join');
        $aRejoinGroup->setAttribute('onclick',"QaRejoinGroup('{$this->parentId}','{$group['group_id']}');");
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