<?php
class HTMLDiv {
  public $HTMLElement;
  function __construct($HTMLDocument,$parent) {
    $this->HTMLElement = $HTMLDocument->createElement( 'div', '' );
    $parent->HTMLElement->appendChild( $this->HTMLElement );
  }
}
?>