<?php
class HTMLHr extends HTMLElement {
	function __construct($parentElement,$width=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'hr',$id,$class);
		if(!empty($width)) { $this->setAttribute( 'width', $width ); }
	}
}
?>