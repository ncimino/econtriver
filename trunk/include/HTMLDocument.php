<?php
class HTMLDocument
{
  public $title = 'eContriver';
  public $document;

  function __construct (  )
  {
    // Create DOMDocument with DOMDocumentType for HTML
    $DOMImplementation = new DOMImplementation( );
    $DOMDocumentType = $DOMImplementation->createDocumentType("html",
                "-//W3C//DTD XHTML TRANSITIONAL 1.0//EN", 
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"); 
    $this->document = $DOMImplementation->createDocument(null, 'html', $DOMDocumentType);
    
    // Create Name Space
    $this->document->createAttributeNS( 'http://www.w3.org/1999/xhtml', 'xmlns' );
    $this->document->documentElement->setAttribute('lang','en');
    
    $body = $this->document->createElement( 'body', '' );
    $this->document->documentElement->appendChild( $body );
  }

  public function createElement ( $nodeName, $nodeValue=null )
  {
    if (strcasecmp($nodeName, 'a') == 0) 
    return $this->document->createElement( $nodeName, $nodeValue );
  }

  public function __toString ( )
  {
    // Create the head element
    $title = $this->document->createElement( 'title', $this->title );
    $this->document->getElementsByTagName('head')->item(0)->appendChild( $title );
    // Add stylesheets if needed
    if ( is_array( $this->styles ))
    foreach ( $this->styles as $element )
    $this->document->getElementsByTagName('head')->item(0)->appendChild( $element );
    // Add scripts if needed
    if(  is_array( $this->scripts ))
    foreach ( $this->scripts as $element )
    $this->document->getElementsByTagName('head')->item(0)->appendChild( $element );
    // Add meta tags if needed
    if ( is_array( $this->metas ))
    foreach ( $this->metas as $element )
    $this->document->getElementsByTagName('head')->item(0)->appendChild( $element );
    
    // Create the document
    //$this->document->documentElement->appendChild( $this->head );
    //$this->document->documentElement->appendChild( $this->body );
    return $this->document->saveXML( );
    //return $this->metas;

    //$meta_tags = $this->metas;
    //print_r($meta_tags[0]);
    
    //$meta_tags = $this->document->getElementsByTagName('meta');
    //print_r($meta_tags->length);
  }

}
?>