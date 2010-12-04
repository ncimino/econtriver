<?php
class HTMLSpan extends HTMLElement {
	function __construct($parentElement,$value,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'span',$value,$id,$class);
	}
}
?>