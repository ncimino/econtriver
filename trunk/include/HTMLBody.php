<?php
class HTMLBody
{
  public $HTMLElement;
  function __construct($HTMLDocument) {
    $this->HTMLElement = $HTMLDocument->createElement( 'body', '' );
    $HTMLDocument->documentElement->appendChild( $this->HTMLElement );
  }
}
?>