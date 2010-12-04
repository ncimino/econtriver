<?php
class HTML_Hr extends HTML_Element {
	function __construct($parentElement,$width=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'hr',$id,$class);
		if(!empty($width)) { $this->setAttribute( 'width', $width ); }
	}
}
?>