<?php
class HTMLLegend extends HTMLElement {
  function __construct($parentElement,$value,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'legend',$value);
    $this->setClassAndId($class,$id);
  }
}
?>