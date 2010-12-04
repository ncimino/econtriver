<?php
class HTML_OptGroup extends HTML_Element {
	function __construct($parentElement,$label,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'optgroup','',$id,$class);
		$this->setAttribute( 'label', $label );
	}
}
?>