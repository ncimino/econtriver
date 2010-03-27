<?php
class HTMLTitle extends HTMLElement {
	function __construct($parentElement,$innerHTML) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'title',$innerHTML);
	}
}
?>