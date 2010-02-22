<?php
class HTMLInputPassword extends HTMLInput {
  function __construct($parentElement,$name) {
    parent::__construct($parentElement,'password',$name);
  }
}
?>