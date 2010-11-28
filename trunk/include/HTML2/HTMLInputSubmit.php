<?php
class HTMLInputSubmit extends HTMLInput {
	function __construct($parentElement,$name,$value=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement,'submit',$name,$id,$class);
		if (!empty($value)) { $this->setAttribute('value',$value); }
	}
}
?>