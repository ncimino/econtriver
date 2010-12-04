<?php
class HTMLStyle extends HTMLElement {
	function __construct($parentElement,$style) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'style');
		$this->setAttribute( 'type', 'text/css' );
		$styleNode = $parentElement->HTMLElement->ownerDocument->createTextNode("\n".$style."\n");
		$this->HTMLElement->appendChild($styleNode);
	}
}
?>