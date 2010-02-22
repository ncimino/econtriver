<?php
class HTMLBr extends HTMLElement {
  function __construct($parentElement,$data=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'br');
  }
}
?>