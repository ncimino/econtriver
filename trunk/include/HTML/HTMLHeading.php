<?php
class HTMLHeading extends HTMLElement {
  function __construct($parentElement,$type,$value=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'h'.$type,$value);
  }
}
?>