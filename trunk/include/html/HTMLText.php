<?php
class HTMLText extends HTMLElement {
	function __construct($parentElement,$data) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement);
		$textNode = $this->createTextNode($parentElement->HTMLElement->ownerDocument,$data);
		$parentElement->HTMLElement->appendChild($textNode);
	}
}
?>