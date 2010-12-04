<?php
class HTML_Strong extends HTML_Element {
	function __construct($parentElement,$value,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'strong',$value,$id,$class);
	}
}
?>