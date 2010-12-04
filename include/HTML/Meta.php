<?php
class HTML_Meta extends HTML_Element {
	function __construct($parentElement) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'meta');
	}
}
?>