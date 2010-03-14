<?php
class HTMLTr extends HTMLElement {
  function __construct($parentElement,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'tr','');
    $this->setClassAndId($class,$id);
  }
}
?>