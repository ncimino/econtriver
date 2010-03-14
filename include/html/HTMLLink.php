<?php
class HTMLLink extends HTMLElement {
  function __construct($parentElement) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'link');
  }
}
?>