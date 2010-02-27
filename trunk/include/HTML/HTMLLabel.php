<?php
class HTMLLabel extends HTMLElement {
  function __construct($parentElement,$label,$forInputId) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'label',$label);
    $this->setAttribute( 'for', $forInputId );
  }
}
?>