<?php
class HTMLElement {
  public $HTMLElement;
  function __construct($HTMLDOMDocument,$parentDOMElement,$tagName=NULL,$innerHTML=NULL) {
    if (!empty($tagName)) {
      $this->createElement($HTMLDOMDocument,$tagName,$innerHTML);
      $this->appendChild($parentDOMElement);
    }
  }
  function appendChild($parentDOMElement) { return $parentDOMElement->appendChild( $this->HTMLElement ); }
  function remove() { return $this->HTMLElement->parentNode->removeChild( $this->HTMLElement ); }
  function createElement($HTMLDOMDocument,$tagName,$innerHTML=NULL) {
    if ($innerHTML==NULL) { return $this->HTMLElement = $HTMLDOMDocument->createElement( $tagName ); }
    else { return $this->HTMLElement = $HTMLDOMDocument->createElement( $tagName, $innerHTML ); }
  }
  function createCDATASection($HTMLDOMDocument,$content) { return $this->HTMLElement = $HTMLDOMDocument->createCDATASection($content); }
  function createComment($HTMLDOMDocument,$data) { return $this->HTMLElement = $HTMLDOMDocument->createComment( $data ); }
  function createTextNode($HTMLDOMDocument,$data) { return $this->HTMLElement = $HTMLDOMDocument->createTextNode( $data ); }
  function replaceData($data) { $this->HTMLElement->replaceData( 0, $this->HTMLElement->length, $data ); }
  function setAttribute($name,$value) { $this->HTMLElement->setAttribute( $name, $value ); }
  function setClass($className) { $this->setAttribute('class',$className); }
  function setId($idName) { $this->setAttribute('id',$idName); }
  function setStyle($styles) { $this->setAttribute('style',$styles); }
  function setTitle($classTitle) { $this->setAttribute('title',$classTitle); }
  function setClassAndId($class,$id=NULL) {
    if(!empty($class)) { $this->setClass( $class ); }
    if(!empty($id)) {
      $this->setId( $id );
    } elseif($id===NULL) {
      if(!empty($class)) { $this->setId( $class.'_'.$this->HTMLElement->nodeName ); }
    }
  }
  function getAttribute($name) { return $this->HTMLElement->getAttribute( $name ); }
  function getId() { return $this->HTMLElement->getAttribute( 'id' ); }
}
?>