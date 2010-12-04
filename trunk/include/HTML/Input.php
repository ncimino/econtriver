<?php
class HTML_Input extends HTML_Element {
	function __construct($parentElement,$type,$name=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'input',NULL,$id,$class);
		$this->setAttribute( 'type', $type );
		if (!empty($name)) { $this->setAttribute( 'name', $name ); }
	}
}
?>