<?php
class HTMLSelect extends HTMLElement {
  function __construct($parentElement,$name=NULL,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'select','');
    if(!empty($name)) { $this->setAttribute( 'name', $name ); }
    if (($class===NULL) and ($id===NULL)) {
      $this->setClassAndId($name);
    } else {
      $this->setClassAndId($class,$id);
    }
  }
}
?>