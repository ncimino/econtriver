<?php
class HTMLOption extends HTMLElement {
	function __construct($parentElement,$innerHTML,$value=NULL,$selected=FALSE,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'option',$innerHTML,$id,$class);
		if (!empty($value)) { $this->setAttribute( 'value', $value ); }
		if ($selected) { $this->setAttribute( 'selected', 'selected' ); }
	}
}
?>