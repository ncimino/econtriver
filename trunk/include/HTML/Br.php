<?php
class HTML_Br extends HTML_Element {
	function __construct($parentElement) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'br');
	}
}
?>