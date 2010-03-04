<?php
class HTMLHr extends HTMLElement {
  function __construct($parentElement,$width=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'hr');
    if(!empty($width)) { $this->setAttribute( 'width', $width ); }
  }
}
?>