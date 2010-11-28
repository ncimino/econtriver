<?php
class HTMLImage extends HTMLElement {
	function __construct($parentElement,$src,$alt,$width=NULL,$height=NULL,$id=NULL,$class=NULL) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'img',NULL,$id,$class);
		$this->setAttribute( 'src', $src );
		$this->setAttribute( 'alt', $alt );
		if(!empty($width)) { $this->setAttribute( 'width', $width ); }
		if(!empty($height)) { $this->setAttribute( 'height', $height ); }
	}
}
?>