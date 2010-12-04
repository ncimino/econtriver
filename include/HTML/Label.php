<?php
class HTML_Label extends HTML_Element {
	function __construct($parentElement,$label,$forInputId,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'label',$label,$id,$class);
		$this->setAttribute( 'for', $forInputId );
	}
}
?>