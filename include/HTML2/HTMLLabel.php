<?php
class HTMLLabel extends HTMLElement {
	function __construct($parentElement,$label,$forInputId,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'label',$label,$id,$class);
		$this->setAttribute( 'for', $forInputId );
	}
}
?>