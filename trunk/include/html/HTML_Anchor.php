<?php
class HTML_Anchor extends HTMLElement {
	function __construct($parentElement,$href,$innerHTML,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'a',$innerHTML,$id,$class);
		$this->setAttribute( 'href', $href );
	}
}
?>