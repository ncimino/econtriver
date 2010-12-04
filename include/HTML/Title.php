<?php
class HTML_Title extends HTML_Element {
	function __construct($parentElement,$innerHTML) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'title',$innerHTML);
	}
}
?>