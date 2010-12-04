<?php
class HTML_Select extends HTML_Element {
	function __construct($parentElement,$name=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'select','',$id,$class);
		if (!empty($name)) { $this->setAttribute( 'name', $name ); }
	}
}
?>