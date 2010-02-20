<?php
class HTMLScript extends HTMLElement {
  function __construct($parentElement,$content=NULL,$URL=NULL) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'script','');
    if(!empty($content)) { $this->createCDATASection($parentElement->HTMLElement->ownerDocument,$content); }
    $this->setAttribute( 'type' , 'text/javascript' );
    if(!empty($URL)) { $this->setAttribute( 'src' , $URL ); }
  }
  function createCDATASection($HTMLDocument,$content) {
    $text = $HTMLDocument->createTextNode("\n/*");
    $this->HTMLElement->appendChild($text);
    $text = parent::createCDATASection($HTMLDocument,"*/\n".$content."\n/*");
    $this->HTMLElement->appendChild($text);
    $text = $HTMLDocument->createTextNode("*/\n");
    $this->HTMLElement->appendChild($text);
  }
}
?>