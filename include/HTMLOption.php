<?php
class HTMLOption extends HTMLElement {
  function __construct($parentElement,$innerHTML,$value=NULL,$selected=FALSE,$class=NULL,$id=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'option',$innerHTML);
    if(!empty($value)) { $this->setAttribute( 'value', $value ); }
    if($selected) { $this->setAttribute( 'selected', 'selected' ); }
    if (($class===NULL) and ($id===NULL)) {
      $this->setClassAndId($name);
    } else {
      $this->setClassAndId($class,$id);
    }
  }
}
?>