<?php
class HTML_Option extends HTML_Element {
	function __construct($parentElement,$innerHTML,$value=NULL,$selected=FALSE,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'option',$innerHTML,$id,$class);
		if ($value == 0 OR !empty($value)) { $this->setAttribute( 'value', $value ); }
		if ($selected) { $this->setAttribute( 'selected', 'selected' ); }
	}
}
?>