<?php
class HTMLUnorderedList extends HTMLElement {
	function __construct($parentElement,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'ul','',$id,$class);
	}
}
?>