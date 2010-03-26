<?php
class HTMLParagraph extends HTMLElement {
  function __construct($parentElement,$data='',$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'p',$data);
    $this->setClassAndId($class,$id);
  }
}
?>