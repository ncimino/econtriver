<?php
class HTMLInputButton extends HTMLInput {
	function __construct($parentElement,$name,$value=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement,'button',$name,$id,$class);
		if (!empty($value)) { $this->setAttribute('value',$value); }
	}
}
?>