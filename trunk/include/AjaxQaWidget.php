<?php
class AjaxQaWidget {
  protected $DB;
  protected $siteInfo;
  protected $infoMsg;
  protected $user;
  protected $document;
  protected $container;

  function __construct() {
    $this->DB = new DBCon();
    $this->siteInfo = new SiteInfo();
    $this->infoMsg = new InfoMsg();
    $this->user = new User($this->DB,'','');
    $this->document = HTMLDocument::createHTMLDocument();
    $this->container = new HTMLFragment($this->document);
    $divInfoMsg = new HTMLDiv($this->container,'qa_messages');
    $this->infoMsg->commitDiv($divInfoMsg);
  }
  
  function printHTML() {
    $this->infoMsg->commitMessages();
    printf( '%s', $this->document->saveXML() );
  }
}
?>