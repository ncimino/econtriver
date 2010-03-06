<?php
class Site {
  public $DB;
  public $siteInfo;
  public $infoMsg;
  public $user;
  public $content;
  public $head;
  public $body;
  public $document;

  function __construct($title) {
    $this->DB = new DBCon();
    $this->siteInfo = new SiteInfo();
    $this->infoMsg = new InfoMsg();
    $this->user = new User($this->DB,$this->siteInfo,$this->infoMsg);
    $this->document = HTMLDocument::createHTMLDocument();
    $this->head = new Head($this->document,$this->siteInfo);
    $this->body = new Body($this->document,$this->infoMsg,$this->siteInfo,$this->user,$title);
    $this->content = $this->body->divMid;
  }

  function printPage() {
    $this->infoMsg->commitMessages();
    printf( '%s', $this->document->saveXML() );
  }
}
?>