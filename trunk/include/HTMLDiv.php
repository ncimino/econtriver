<?php
class HTMLDiv extends HTMLElement {
  function __construct($parentElement) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'div','');
  }
}
?>