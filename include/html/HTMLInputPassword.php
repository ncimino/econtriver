<?php
class HTMLInputPassword extends HTMLInput {
	function __construct($parentElement,$name,$id=NULL,$class=NULL) {
		parent::__construct($parentElement,'password',$name,$id,$class);
	}
}
?>