<?php
class HTMLKeywords extends HTMLMeta {
  function __construct($parentElement,$content) {
    parent::__construct($parentElement,$parentElement);
    $this->setAttribute( 'name', 'keywords' );
    $this->setAttribute( 'content', $content );
  }
}
?>