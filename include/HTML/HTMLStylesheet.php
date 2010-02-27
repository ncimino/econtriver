<?php
class HTMLStylesheet extends HTMLLink {
  function __construct($parentElement,$iconLocation) {
    parent::__construct($parentElement);
    $this->setAttribute( 'rel', 'stylesheet' );
    $this->setAttribute( 'type', 'text/css' );
    $this->setAttribute( 'href', $iconLocation );
  }
}
?>