<?php
class HTMLParagraph extends HTMLElement {
  function __construct($parentElement,$data=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'p',$data);
  }
}
?>