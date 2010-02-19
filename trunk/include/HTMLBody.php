<?php
class HTMLBody extends HTMLElement {
  function __construct($HTMLDocument) {
    parent::__construct($HTMLDocument->HTMLDocument,$HTMLDocument->HTMLDocument->documentElement,'body','');
  }
}
?>