<?php
class HTMLOrderedList extends HTMLElement {
	function __construct($parentElement,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'dl','',$id,$class);
	}
}
?>