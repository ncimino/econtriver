<?php
class HTMLMeta extends HTMLElement {
	function __construct($parentElement) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'meta');
	}
}
?>