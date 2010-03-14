<?php
class HTMLImage extends HTMLElement {
  function __construct($parentElement,$src,$alt,$width=NULL,$height=NULL,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'img');
    $this->setAttribute( 'src', $src );
    $this->setAttribute( 'alt', $alt );
    if(!empty($width)) { $this->setAttribute( 'width', $width ); }
    if(!empty($height)) { $this->setAttribute( 'height', $height ); }
    $this->setClassAndId($class,$id);
  }
}
?>