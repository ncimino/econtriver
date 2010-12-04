<?php
class HTML_Heading extends HTML_Element {
	function __construct($parentElement,$type,$value=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'h'.$type,$value,$id,$class);
	}
}
?>