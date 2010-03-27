<?php
class HTMLLegend extends HTMLElement {
	function __construct($parentElement,$value,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'legend',$value,$id,$class);
	}
}
?>