<?php
class Widget {
  protected $parentElement;
  protected $DB;
  protected $siteInfo;
  protected $infoMsg;
  protected $user;
  private $focusId = false;
  private $containerId = false;

  function __construct($parentElement,$DB,$siteInfo,$infoMsg,$user) {
    $this->parentElement = $parentElement;
    $this->DB = $DB;
    $this->siteInfo = $siteInfo;
    $this->infoMsg = $infoMsg;
    $this->user = $user;
  }

  function getFocusId() { return $this->focusId; }
  function getContainerId() { return $this->containerId; }
  
  function setFocusId($id) { $this->focusId = $id; }
  function setContainerId($id) { $this->containerId = $id; }
}
?>