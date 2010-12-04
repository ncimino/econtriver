<?php
class HTMLListItem extends HTMLElement {
	function __construct($parentElement,$value='',$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'li',$value,$id,$class);
	}
}
?>