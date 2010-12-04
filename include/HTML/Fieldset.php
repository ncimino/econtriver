<?php
class HTMLFieldset extends HTMLElement {
	function __construct($parentElement,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'fieldset','',$id,$class);
	}
}
?>