<?php
class HTMLInput extends HTMLElement {
  function __construct($parentElement,$type,$name=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'input');
    $this->setAttribute( 'type', $type );
    if(!empty($name)) { $this->setAttribute( 'name', $name ); }
  }
}
?>