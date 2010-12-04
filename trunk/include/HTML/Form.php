<?php
class HTML_Form extends HTML_Element {
	function __construct($parentElement,$action='',$id=NULL,$class=NULL,$method='post') {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'form','',$id,$class);
		$this->setAttribute( 'method', $method );
		$this->setAttribute( 'action', $action);
	}
}
?>