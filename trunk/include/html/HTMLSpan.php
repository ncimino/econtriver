<?php
class HTMLSpan extends HTMLElement {
  function __construct($parentElement,$value,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'span',$value);
    $this->setClassAndId($class,$id);
  }
}
?>