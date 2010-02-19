<?php
class HTMLElement {
  public $HTMLDocument;
  function __construct($HTMLDocument) {
    $this->HTMLDocument = $HTMLDocument;
  }
  function appendChild($HTMLElement) {
    $this->HTMLDocument->appendChild( $HTMLElement );
  }
}
?>