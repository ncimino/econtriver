<?php
class AjaxQaWidget {
  protected $DB;
  protected $siteInfo;
  protected $infoMsg;
  protected $user;
  protected $document;
  protected $container;
  
  const main = 'quick_accts';
  
  static function getMainClass() { return self::main; }
  static function getQaMsgsClass() { return self::getMainClass().'_msgs'; }
  static function getQaMsgsId() { return self::getQaMsgsClass().'_div'; }

  function __construct() {
    $this->DB = new DBCon();
    $this->siteInfo = new SiteInfo();
    $this->infoMsg = new InfoMsg();
    $this->user = new User($this->DB,'','');
    $this->document = HTMLDocument::createHTMLDocument();
    $this->container = new HTMLFragment($this->document);
    $divInfoMsg = new HTMLDiv($this->container,self::getQaMsgsClass(),self::getQaMsgsId());
    $this->infoMsg->commitDiv($divInfoMsg);
  }
  
  function printHTML() {
    $this->infoMsg->commitMessages();
    //printf( '%s', $this->document->saveXML() );
    printf( '%s', Normalize::innerFragment($this->document) );
  }
}
?>