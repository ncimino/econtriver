<?php
class HTMLStyle extends HTMLElement {
  function __construct($parentElement,$style) {
    parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'style');
    $this->setAttribute( 'type', 'text/css' );
    //    $textNode[0] = $parentElement->HTMLElement->ownerDocument->createTextNode("\n/*");
    //    $this->HTMLElement->appendChild($textNode[0]);
    //    $styleNode = $parentElement->HTMLElement->ownerDocument->createCDATASection("*/\n".$style."\n/*");
    //    $this->HTMLElement->appendChild($styleNode);
    //    $textNode[1] = $parentElement->HTMLElement->ownerDocument->createTextNode("*/\n");
    //    $this->HTMLElement->appendChild($textNode[1]);
    $styleNode = $parentElement->HTMLElement->ownerDocument->createTextNode("\n".$style."\n");
    $this->HTMLElement->appendChild($styleNode);
  }
}
?>