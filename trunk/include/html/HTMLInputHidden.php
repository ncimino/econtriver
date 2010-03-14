<?php
class HTMLInputHidden extends HTMLInput {
  function __construct($parentElement,$name,$value=NULL) {
    parent::__construct($parentElement,'hidden',$name);
    if (!empty($value)) { $this->setAttribute('value',$value); }
  }
}
?>