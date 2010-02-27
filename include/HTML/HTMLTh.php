<?php
class HTMLTh extends HTMLElement {
  function __construct($parentElement,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'th','');
    $this->setClassAndId($class,$id);
  }
}
?>