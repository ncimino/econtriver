<?php
class HTMLSelect extends HTMLElement {
	function __construct($parentElement,$name=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'select','',$id,$class);
		if (!empty($name)) { $this->setAttribute( 'name', $name ); }
	}
}
?>