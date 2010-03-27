<?php
class HTMLOptGroup extends HTMLElement {
	function __construct($parentElement,$label,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'optgroup','',$id,$class);
		$this->setAttribute( 'label', $label );
	}
}
?>