<?php
class HTMLForm extends HTMLElement {
  function __construct($parentElement,$action='',$class=NULL,$id=NULL,$method='post') {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'form','');
    $this->setAttribute( 'method', $method );
    $this->setAttribute( 'action', $action);
    $this->setClassAndId($class,$id);
  }
}
?>