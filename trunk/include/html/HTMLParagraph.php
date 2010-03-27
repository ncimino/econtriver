<?php
class HTMLParagraph extends HTMLElement {
	function __construct($parentElement,$data='',$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'p',$data,$id,$class);
	}
}
?>