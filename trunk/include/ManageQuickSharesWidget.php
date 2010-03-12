<?php
class ManageQuickSharesWidget {
  private $focusId = '';
  private $infoMsg;
  private $parentElement;
  private $DB;
  private $siteInfo;
  private $user;
  private $groups; // MySQL result

  private $createGrpName = '';
  private $dropGrpId = false;
  private $editGrpId = false;
  private $editGrpName = false;

  const createGrpDiv = 'create_group';
  const createGrpForm = 'create_group';
  const createGrpText = 'group_name';
  const createGrpTextId = 'group_name_input';
  const createGrpButton = 'group_button';

  const editGrpNameDiv = 'edit_group_name';
  const editGrpNameForm = 'edit_group_name';
  const editGrpNameHidden = 'edit_group_id';
  const editGrpNameText = 'new_group_name';
  const editGrpNameTextId = 'new_group_name_input';
  const editGrpNameButton = 'edit_group_button';

  const editGrpForm = 'edit_group';
  const editGrpHidden = 'edit_group';
  const editGrpHiddenId = 'edit_group_input';
  const editGrpButton = 'edit_group_submit';

  const dropGrpForm = 'drop_group';
  const dropGrpHidden = 'drop_group';
  const dropGrpHiddenId = 'drop_group_input';
  const dropGrpButton = 'drop_group_submit';

  const grpDiv = 'groups';
  const grpTable = 'groups';

  function __construct($parentElement,$DB,$siteInfo,$infoMsg,$user) {
    $this->infoMsg = $infoMsg;
    $this->parentElement = $parentElement;
    $this->DB = $DB;
    $this->siteInfo = $siteInfo;
    $this->user = $user;
    $this->setFromPost();
    if ($this->checkGroupName($this->getCreateGrpName())) {
      $this->insertGroup();
      $this->insertGroupUser();
    } else {
      $this->focusId = self::createGrpTextId;
    }
    if ($this->getEditGrpId() and $this->getEditGrpName() and $this->checkGroupName($this->getEditGrpName())) {
      if ($this->updateGroup()) {
        $this->infoMsg->addMessage(2,'Group was successsfully updated.');
      }
    }
    if ($this->getDropGrpId()) {
      if ($this->dropGroup()) {
        $this->infoMsg->addMessage(2,'Group was successsfully deleted.');
      }
    }
    $this->getGroups();
    $this->buildWidget();
  }

  function buildWidget() {
    $divQuickGroups = new HTMLDiv($this->parentElement,'quick_groups');
    new HTMLHeading($divQuickGroups,4,'Group Management');
    if ($this->getEditGrpId()) {
      $this->addEditGroupForm($divQuickGroups);
    }
    $this->addCreateGroupForm($divQuickGroups);
    $this->addGroupsTable($divQuickGroups);
  }

  function getFocusId() { return $this->focusId; }
  function getCreateGrpName() { return $this->createGrpName; }
  function getDropGrpId() { return $this->dropGrpId; }
  function getEditGrpId() { return $this->editGrpId; }
  function getEditGrpName() { return $this->editGrpName; }

  function setCreateGrpName($name) { $this->createGrpName = $name; }
  function setDropGrpId($id) { $this->dropGrpId = $id; }
  function setEditGrpId($id) { $this->editGrpId = $id; }
  function setEditGrpName($id) { $this->editGrpName = $id; }

  function setFromPost() {
    if(isset($_POST[self::editGrpHidden])) { $this->setEditGrpId($_POST[self::editGrpHidden]); }
    elseif(isset($_POST[self::editGrpNameHidden])) { $this->setEditGrpId($_POST[self::editGrpNameHidden]); }
    if(isset($_POST[self::createGrpText])) { $this->setCreateGrpName($_POST[self::createGrpText]); }
    if(isset($_POST[self::editGrpNameText])) { $this->setEditGrpName($_POST[self::editGrpNameText]); }
    if(isset($_POST[self::dropGrpHidden])) { $this->setDropGrpId($_POST[self::dropGrpHidden]); }
  }

  function checkGroupName($name) {
    if (!($sanitizedName = Normalize::sanitize($name,$this->infoMsg,$this->siteInfo))) {
      return false;
    } elseif (!(Normalize::groupNames($sanitizedName,$this->infoMsg))) {
      return false;
    } else {
      return true;
    }
  }

  function insertGroup() {
    $groupNameEscaped = Normalize::mysql($this->getCreateGrpName());
    $sql = "INSERT INTO q_group (name)
VALUES ('{$groupNameEscaped}');";
    return $this->DB->query($sql);
  }

  function insertGroupUser() {
    $sql = "INSERT INTO q_user_groups (group_id,user_id)
VALUES ({$this->DB->lastID()},{$this->user->getUserId()});";
    return $this->DB->query($sql);
  }

  function dropGroup() {
    $sql = "DELETE FROM q_user_groups WHERE group_id = {$this->getDropGrpId()};";
    return $this->DB->query($sql);
    $sql = "DELETE FROM q_group WHERE id = {$this->getDropGrpId()};";
    return $this->DB->query($sql);
  }

  function updateGroup() {
    $groupNameEscaped = Normalize::mysql($this->getEditGrpName());
    $sql = "UPDATE q_group SET name = '{$groupNameEscaped}' WHERE id = {$this->getEditGrpId()};";
    return $this->DB->query($sql);
  }

  function getGroupNameById($id) {
    $sql = "SELECT name FROM q_group
    		WHERE id = {$id};";
    $this->DB->query($sql);
    $return = $this->DB->fetch();
    return $return['name'];
  }

  function getGroups() {
    $sql = "SELECT * FROM q_group,q_user_groups
    		WHERE q_group.id = group_id 
    		  AND user_id = {$this->user->getUserId()};";
    $this->groups = $this->DB->query($sql);
  }

  function addGroupsTable($parentElement) {
    $divGroups = new HTMLDiv($parentElement,self::grpDiv);
    if ($this->DB->num($this->groups)>0) {
      new HTMLHeading($divGroups,5,'Groups:');
      $tableListGroups = new Table($divGroups,$this->DB->num($this->groups),3,self::grpTable);
      $i = 0;
      while ($group = $this->DB->fetch($this->groups)) {
        new HTMLText($tableListGroups->cells[$i][0],$group['name']);

        $formEditGroup = new HTMLForm($tableListGroups->cells[$i][1],$this->siteInfo->getSelfFileName(),self::editGrpForm);
        new HTMLInputHidden($formEditGroup,self::editGrpHidden,$group['group_id']);
        new HTMLInputSubmit($formEditGroup,self::editGrpButton,'Edit');

        $formDropGroup = new HTMLForm($tableListGroups->cells[$i++][2],$this->siteInfo->getSelfFileName(),self::dropGrpForm);
        new HTMLInputHidden($formDropGroup,self::dropGrpHidden,$group['group_id']);
        $inputDelete = new HTMLInputSubmit($formDropGroup,self::dropGrpButton,'Delete');
        $inputDelete->setAttribute('onclick','return confirmSubmit("Are you sure you want to delete the \''.$group['name'].'\' group?")');
      }
    } else {
      $this->infoMsg->addMessage(2,'You don\'t belong to any groups.  Add a group to get started with shared accounts.');
    }
  }

  function addEditGroupForm($parentElement) {
    $divEditGroup = new HTMLDiv($parentElement,self::editGrpNameDiv);
    new HTMLHeading($divEditGroup,5,'Edit Group:');
    $formEditGroup = new HTMLForm($divEditGroup,$this->siteInfo->getSelfFileName(),self::editGrpNameForm);
    new HTMLInputHidden($formEditGroup,self::editGrpNameHidden,$this->getEditGrpId());
    $editGroupName = $this->getEditGrpName();
    $groupName = (empty($editGroupName)) ? $this->getGroupNameById($this->getEditGrpId()) : $this->getEditGrpName();
    new HTMLInputText($formEditGroup,self::editGrpNameText,$groupName);
    new HTMLInputSubmit($formEditGroup,self::editGrpNameButton,'Edit Group');
    $this->focusId = self::editGrpNameTextId;
  }

  function addCreateGroupForm($parentElement) {
    $divAddGroup = new HTMLDiv($parentElement,self::createGrpDiv);
    new HTMLHeading($divAddGroup,5,'Add Group:');
    $formAddGroup = new HTMLForm($divAddGroup,$this->siteInfo->getSelfFileName(),self::createGrpForm);
    new HTMLInputText($formAddGroup,self::createGrpText,$this->getCreateGrpName());
    new HTMLInputSubmit($formAddGroup,self::createGrpButton,'Add Group');
  }
}
?>