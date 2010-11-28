<?php
class HTMLTh extends HTMLElement {
	function __construct($parentElement,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'th','',$id,$class);
	}
}
?>