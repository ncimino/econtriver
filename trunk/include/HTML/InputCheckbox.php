<?php
class HTML_InputCheckbox extends HTML_Input {
	function __construct($parentElement,$name,$id=NULL,$class=NULL,$selected=FALSE) {
		parent::__construct($parentElement,'checkbox',$name,$id,$class);
		if ($selected) { $this->setAttribute( 'checked', 'checked' ); }
	}
}
?>