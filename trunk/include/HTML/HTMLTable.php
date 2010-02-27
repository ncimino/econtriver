<?php
class HTMLTable extends HTMLElement {
  function __construct($parentElement,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'table','');
    $this->setClassAndId($class,$id);
  }
}
?>