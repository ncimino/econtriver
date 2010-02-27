<?php
class HTMLTd extends HTMLElement {
  function __construct($parentElement,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'td','');
    $this->setClassAndId($class,$id);
  }
}
?>