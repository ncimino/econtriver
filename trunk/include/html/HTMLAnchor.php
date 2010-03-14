<?php
class HTMLAnchor extends HTMLElement {
  function __construct($parentElement,$href,$innerHTML,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'a',$innerHTML);
    $this->setAttribute( 'href', $href );
    $this->setClassAndId($class,$id);
  }
}
?>