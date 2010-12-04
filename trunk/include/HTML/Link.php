<?php
class HTML_Link extends HTML_Element {
	function __construct($parentElement) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'link');
	}
}
?>