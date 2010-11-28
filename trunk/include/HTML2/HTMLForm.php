<?php
class HTMLForm extends HTMLElement {
	function __construct($parentElement,$action='',$id=NULL,$class=NULL,$method='post') {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'form','',$id,$class);
		$this->setAttribute( 'method', $method );
		$this->setAttribute( 'action', $action);
	}
}
?>