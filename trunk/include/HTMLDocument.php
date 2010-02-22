<?php
class HTMLDocument extends DOMImplementation {
  function __construct() {
    $this->siteInfo = new SiteInfo();
  }
  static function createHTMLDocument($qualifiedName = "html",
  $publicId = "-//W3C//DTD XHTML 1.0 Transitional//EN",
  //$publicId = "-//W3C//DTD XHTML 1.0 Strict//EN",
  $systemId = "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd",
  //$systemId = "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd",
  $namespaceURI = NULL) {
    $HTMLImplementation = new DOMImplementation();
    $HTMLDocumentType = $HTMLImplementation->createDocumentType($qualifiedName, $publicId, $systemId);
    return $HTMLImplementation->createDocument($namespaceURI, $qualifiedName, $HTMLDocumentType);
  }
}
?>