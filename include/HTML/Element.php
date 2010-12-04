<?php
class HTMLElement {
	public $HTMLElement;
	function __construct($HTMLDOMDocument,$parentDOMElement,$tagName=NULL,$innerHTML=NULL,$id=NULL,$class=NULL) {
		if (!empty($tagName)) {
			$this->createElement($HTMLDOMDocument,$tagName,$innerHTML);
			$this->appendChild($parentDOMElement);
			if (!empty($id)) { $this->setId($id); }
			if (!empty($class)) { $this->setClass($class); }
		}
	}
	/*
	 * Creators
	 */
	function createElement($HTMLDOMDocument,$tagName,$innerHTML=NULL) {
		if ($innerHTML==NULL) { return $this->HTMLElement = $HTMLDOMDocument->createElement( $tagName ); }
		else { return $this->HTMLElement = $HTMLDOMDocument->createElement( $tagName, $innerHTML ); }
	}
	function createCDATASection($HTMLDOMDocument,$content) { return $this->HTMLElement = $HTMLDOMDocument->createCDATASection($content); }
	function createComment($HTMLDOMDocument,$data) { return $this->HTMLElement = $HTMLDOMDocument->createComment( $data ); }
	function createTextNode($HTMLDOMDocument,$data) { return $this->HTMLElement = $HTMLDOMDocument->createTextNode( $data ); }
	/*
	 * Modifiers
	 */
	function appendChild($parentDOMElement) { return $parentDOMElement->appendChild( $this->HTMLElement ); }
	function remove() { return $this->HTMLElement->parentNode->removeChild( $this->HTMLElement ); }
	function replaceData($data) { $this->HTMLElement->replaceData( 0, $this->HTMLElement->length, $data ); }
	function removeAttribute($name) { $this->HTMLElement->removeAttribute( $name ); }
	/*
	 * Setters
	 */
	function setAttribute($name,$value) { $this->HTMLElement->setAttribute( $name, $value ); }
	function setClass($className) { $this->setAttribute('class',$className); }
	function setId($idName) { $this->setAttribute('id',$idName); }
	function setStyle($styles) { $this->setAttribute('style',$styles); }
	function setTitle($classTitle) { $this->setAttribute('title',$classTitle); }
	/*
	 * Getters
	 */
	function getAttribute($name) { return $this->HTMLElement->getAttribute( $name ); }
	function getClass() { return $this->HTMLElement->getAttribute( 'class' ); }
	function getId() { return $this->HTMLElement->getAttribute( 'id' ); }
}
?>