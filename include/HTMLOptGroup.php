<?php
class HTMLOptGroup extends HTMLElement {
  function __construct($parentElement,$label,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'optgroup','');
    $this->setAttribute( 'label', $label );
    $this->setClassAndId($class,$id);
  }
}
?>