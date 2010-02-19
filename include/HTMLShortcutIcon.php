<?php
class HTMLShortcutIcon extends HTMLLink {
  function __construct($HTMLDocument,$parentElement,$iconLocation,$type) {
    parent::__construct($HTMLDocument,$parentElement);
    $this->setAttribute( 'rel', 'shortcut icon' );
    $this->setAttribute( 'href', $iconLocation );
    $this->setAttribute( 'type', $type );

  }
}
?>