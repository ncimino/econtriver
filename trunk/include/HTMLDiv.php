<?php
class HTMLDiv extends HTMLElement {
  function __construct($parentElement,$class=NULL,$id='') {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'div','');
    $this->setClassAndId($class,$id);
  }
}
?>