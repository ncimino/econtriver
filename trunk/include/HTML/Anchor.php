<?php
class HTML_Anchor extends HTML_Element {
	function __construct($parentElement,$href,$innerHTML,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'a',$innerHTML,$id,$class);
		$this->setAttribute( 'href', $href );
	}
}
?>