<?php
class HTML_Document extends DOMImplementation {
	function __construct() { }
	static function create($qualifiedName = "html",
	$publicId = "-//W3C//DTD XHTML 1.0 Transitional//EN",
	//$publicId = "-//W3C//DTD XHTML 1.0 Strict//EN",
	$systemId = "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd",
	//$systemId = "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd",
	$namespaceURI = NULL) {
		$HTMLImplementation = new DOMImplementation();
		$HTML_DocumentType = $HTMLImplementation->createDocumentType($qualifiedName, $publicId, $systemId);
		return $HTMLImplementation->createDocument($namespaceURI, $qualifiedName, $HTML_DocumentType);
	}
}
?>