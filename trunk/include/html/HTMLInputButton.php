<?php
class HTMLInputButton extends HTMLInput {
  function __construct($parentElement,$name,$value=NULL,$class=NULL,$id=NULL) {
    parent::__construct($parentElement,'button',$name);
    if (($class===NULL) and ($id===NULL)) {
      $this->setClassAndId($name);
    } else {
      $this->setClassAndId($class,$id);
    }
    if (!empty($value)) { $this->setAttribute('value',$value); }
  }
}
?>