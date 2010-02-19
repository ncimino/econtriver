<?php
class HTMLElement {
  public $HTMLElement;
  public $tagName;
  public $innerHTML;
  function __construct($HTMLDocument,$parentElement,$tagName,$innerHTML=NULL) {
    $this->tagName=$tagName;
    $this->innerHTML=$innerHTML;
    $this->createElement($HTMLDocument,$tagName,$innerHTML);
    $this->appendChild($parentElement);
  }
  function appendChild($parentElement) {
    $parentElement->appendChild( $this->HTMLElement );
  }
  function createElement($HTMLDocument,$tagName,$innerHTML=NULL) {
    if ($innerHTML==NULL) {
      $this->HTMLElement = $HTMLDocument->createElement( $tagName );
    } else {
      $this->HTMLElement = $HTMLDocument->createElement( $tagName, $innerHTML );
    }
  }
  function createTextNode($HTMLDocument,$content) {
    $this->innerHTML = $HTMLDocument->createTextNode( $content );
    $this->HTMLElement->appendChild($this->innerHTML);
  }
  function setAttribute($name,$value) {
    $this->HTMLElement->setAttribute( $name, $value );
  }
}
?>