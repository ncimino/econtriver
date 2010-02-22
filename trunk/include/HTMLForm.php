<?php
class HTMLForm extends HTMLElement {
  function __construct($parentElement,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'form','');
    $this->setAttribute( 'method', 'post' );
    $this->setAttribute( 'action', '');
    $this->setClassAndId($class,$id);
  }
}
?>