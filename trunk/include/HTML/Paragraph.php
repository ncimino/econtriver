<?php
class HTML_Paragraph extends HTML_Element {
	function __construct($parentElement,$data='',$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'p',$data,$id,$class);
	}
}
?>