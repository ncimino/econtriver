<?php
class HTMLDescription extends HTMLMeta {
  function __construct($parentElement,$content) {
    parent::__construct($parentElement);
    $this->setAttribute( 'name', 'description' );
    $this->setAttribute( 'content', $content );
  }
}
?>