<?php
class HTML_Image extends HTML_Element {
	function __construct($parentElement,$src,$alt,$width=NULL,$height=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'img',NULL,$id,$class);
		$this->setAttribute( 'src', $src );
		$this->setAttribute( 'alt', $alt );
		if(!empty($width)) { $this->setAttribute( 'width', $width ); }
		if(!empty($height)) { $this->setAttribute( 'height', $height ); }
	}
}
?>