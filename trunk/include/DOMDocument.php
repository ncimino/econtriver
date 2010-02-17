<?php
class DOMDocument {
  private $documentMode = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
  private $domain;
  private $referrer;
  private $title = "Undefined Title";
  private $URL;
  
  public function __construct($title="") {
    if (! empty($title)) { $this->title = $title; }
    $this->updateDomain();
    $this->updateReferrer();
    $this->updateURL();
  }
  
  // Update methods
  private function updateDomain() { setDomain($_SERVER['SERVER_NAME']); }
  private function updateReferrer() { setReferrer($_SERVER['HTTP_REFERER']); }
  private function updateURL() { setURL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); }
  
  // Setter methods
  public function setDocumentMode($documentMode) { $this->documentMode = $documentMode; }
  public function setTitle($title) { $this->title = $title; }
  private function setDomain($domain) { $this->domain = $domain; }
  private function setReferrer($referrer) { $this->referrer = $referrer; }
  private function setURL($URL) { $this->URL = $URL; }
  
  // Getter methods
  public function getDocumentMode() { return $this->documentMode; }
  public function getDomain() { return $this->domain; }
  public function getReferrer() { return $this->referrer; }
  public function getTitle() { return $this->title; }
  public function getURL() { return $this->URL; }
}