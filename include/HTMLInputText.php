<?php
class HTMLInputText extends HTMLInput {
  function __construct($parentElement,$name,$class=NULL,$id=NULL) {
    parent::__construct($parentElement,'text',$name);
    if (($class===NULL) and ($id===NULL)) {
      $this->setClassAndId($name);
    } else {
      $this->setClassAndId($class,$id);
    }
  }
}
?>