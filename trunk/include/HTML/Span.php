<?php
class HTML_Span extends HTML_Element {
	function __construct($parentElement,$value,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'span',$value,$id,$class);
	}
}
?>