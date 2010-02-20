<?php
abstract class HTMLElement {
  public $HTMLElement;
  function __construct($HTMLDocument,$parentElement,$tagName,$innerHTML=NULL) {
    $this->createElement($HTMLDocument,$tagName,$innerHTML);
    $this->appendChild($parentElement);
  }
  function appendChild($parentElement) { return $parentElement->appendChild( $this->HTMLElement ); }
  function createElement($HTMLDocument,$tagName,$innerHTML=NULL) {
    if ($innerHTML==NULL) { return $this->HTMLElement = $HTMLDocument->createElement( $tagName ); } 
    else { return $this->HTMLElement = $HTMLDocument->createElement( $tagName, $innerHTML ); }
  }
  function createCDATASection($HTMLDocument,$content) { return $HTMLDocument->createCDATASection($content); }
  function createComment($HTMLDocument,$data) { return $HTMLDocument->createComment( $data ); }
  function createTextNode($HTMLDocument,$data) { return $HTMLDocument->createTextNode( $data ); }
  function setAttribute($name,$value) { $this->HTMLElement->setAttribute( $name, $value ); }
  function setClass($className) { $this->setAttribute('class',$className); }
  function setId($idName) { $this->setAttribute('id',$idName); }
  function setStyle($styles) { $this->setAttribute('style',$styles); }
  function setTitle($classTitle) { $this->setAttribute('title',$classTitle); }
}
?>