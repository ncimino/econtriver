<?php
class HTML {
  public $HTMLDocument;
  function __construct() {
    $HTMLImplementation = new DOMImplementation();
    $HTMLDocumentType = $HTMLImplementation->createDocumentType("html",
                    "-//W3C//DTD XHTML TRANSITIONAL 1.0//EN", 
                    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"); 
    $this->HTMLDocument = $HTMLImplementation->createDocument(null, 'html', $HTMLDocumentType);
  }
}