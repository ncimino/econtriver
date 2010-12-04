<?php
class HTML_Th extends HTML_Element {
	function __construct($parentElement,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'th','',$id,$class);
	}
}
?>