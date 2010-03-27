<?php
class HTMLInput extends HTMLElement {
	function __construct($parentElement,$type,$name=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'input',NULL,$id,$class);
		$this->setAttribute( 'type', $type );
		if (!empty($name)) { $this->setAttribute( 'name', $name ); }
	}
}
?>