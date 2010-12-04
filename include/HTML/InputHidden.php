<?php
class HTML_InputHidden extends HTML_Input {
	function __construct($parentElement,$name,$value=NULL,$id=NULL) {
		parent::__construct($parentElement,'hidden',$name,$id);
		if (!empty($value)) { $this->setAttribute('value',$value); }
	}
}
?>