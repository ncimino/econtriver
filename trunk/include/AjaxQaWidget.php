<?php
class AjaxQaWidget {
  protected $DB;
  protected $siteInfo;
  protected $infoMsg;
  protected $user;
  protected $document;
  protected $container;
  
  const main = 'quick_accts';
  
  function getMainClass() { return self::main; }
  function getQaMsgsClass() { return self::main.'_msgs'; }

  function __construct() {
    $this->DB = new DBCon();
    $this->siteInfo = new SiteInfo();
    $this->infoMsg = new InfoMsg();
    $this->user = new User($this->DB,'','');
    $this->document = HTMLDocument::createHTMLDocument();
    $this->container = new HTMLFragment($this->document);
    $divInfoMsg = new HTMLDiv($this->container,self::getQaMsgsClass());
    $this->infoMsg->commitDiv($divInfoMsg);
  }
  
  function printHTML() {
    $this->infoMsg->commitMessages();
    //printf( '%s', $this->document->saveXML() );
    printf( '%s', Normalize::innerFragment($this->document) );
  }
}
?>