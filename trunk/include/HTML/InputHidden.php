<?php
class HTML_InputHidden extends HTML_Input {
	function __construct($parentElement,$name,$value=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement,'hidden',$name,$id,$class);
		if (!empty($value)) { $this->setAttribute('value',$value); }
	}
}
?>