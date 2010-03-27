<?php
class HTMLInputHidden extends HTMLInput {
	function __construct($parentElement,$name,$value=NULL,$id=NULL) {
		parent::__construct($parentElement,'hidden',$name,$id);
		if (!empty($value)) { $this->setAttribute('value',$value); }
	}
}
?>