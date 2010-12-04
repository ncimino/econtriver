<?php
class HTML_Text extends HTML_Element {
	function __construct($parentElement,$data) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element);
		$textNode = $this->createTextNode($parentElement->HTML_Element->ownerDocument,$data);
		$parentElement->HTML_Element->appendChild($textNode);
	}
}
?>