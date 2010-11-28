<?php
class HTMLHeading extends HTMLElement {
	function __construct($parentElement,$type,$value=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'h'.$type,$value,$id,$class);
	}
}
?>