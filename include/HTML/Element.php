<?php
class HTML_Element {
	public $HTML_Element;
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
		if ($innerHTML==NULL) { return $this->HTML_Element = $HTMLDOMDocument->createElement( $tagName ); }
		else { return $this->HTML_Element = $HTMLDOMDocument->createElement( $tagName, $innerHTML ); }
	}
	function createCDATASection($HTMLDOMDocument,$content) { return $this->HTML_Element = $HTMLDOMDocument->createCDATASection($content); }
	function createComment($HTMLDOMDocument,$data) { return $this->HTML_Element = $HTMLDOMDocument->createComment( $data ); }
	function createTextNode($HTMLDOMDocument,$data) { return $this->HTML_Element = $HTMLDOMDocument->createTextNode( $data ); }
	/*
	 * Modifiers
	 */
	function appendChild($parentDOMElement) { return $parentDOMElement->appendChild( $this->HTML_Element ); }
	function remove() { return $this->HTML_Element->parentNode->removeChild( $this->HTML_Element ); }
	function replaceData($data) { $this->HTML_Element->replaceData( 0, $this->HTML_Element->length, $data ); }
	function removeAttribute($name) { $this->HTML_Element->removeAttribute( $name ); }
	/*
	 * Setters
	 */
	function setAttribute($name,$value) { $this->HTML_Element->setAttribute( $name, $value ); }
	function setClass($className) { $this->setAttribute('class',$className); }
	function setId($idName) { $this->setAttribute('id',$idName); }
	function setStyle($styles) { $this->setAttribute('style',$styles); }
	function setTitle($classTitle) { $this->setAttribute('title',$classTitle); }
	/*
	 * Getters
	 */
	function getAttribute($name) { return $this->HTML_Element->getAttribute( $name ); }
	function getClass() { return $this->HTML_Element->getAttribute( 'class' ); }
	function getId() { return $this->HTML_Element->getAttribute( 'id' ); }
}
?>