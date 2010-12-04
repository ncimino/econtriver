<?php
class HTML_Fieldset extends HTML_Element {
	function __construct($parentElement,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'fieldset','',$id,$class);
	}
}
?>