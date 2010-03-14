<?php
class HTMLFieldset extends HTMLElement {
  function __construct($parentElement,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'fieldset','');
    $this->setClassAndId($class,$id);
  }
}
?>