<?php
class HTML_Style extends HTML_Element {
	function __construct($parentElement,$style) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'style');
		$this->setAttribute( 'type', 'text/css' );
		$styleNode = $parentElement->HTML_Element->ownerDocument->createTextNode("\n".$style."\n");
		$this->HTML_Element->appendChild($styleNode);
	}
}
?>