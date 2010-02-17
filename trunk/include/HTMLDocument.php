<?php
class HTMLDocument
{
  public $doctype;
  public $head;
  public $title = 'eContriver';
  public $body;
  private $styles; // remove
  private $metas; // remove
  private $scripts; // remove
  private $document;

  function __construct (  )
  {
    // Create DOMDocument with DOMDocumentType
    $DOMImp = new DOMImplementation( );
    $this->doctype = $DOMImp->createDocumentType("html",
                "-//W3C//DTD XHTML TRANSITIONAL 1.0//EN", 
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"); 
    $this->document = $DOMImp->createDocument(null, 'html', $this->doctype);
    
    // Creaet Name Space
    $this->document->createAttributeNS( 'http://www.w3.org/1999/xhtml', 'xmlns' );
    $this->document->documentElement->setAttribute('lang','en');
   
    $this->head = $this->document->createElement( 'head', '' );
    $this->body = $this->document->createElement( 'body', '' );
  }


  public function addStyleSheet ( $url, $media='all' )
  {
    $element = $this->document->createElement( 'link' );
    $element->setAttribute( 'type', 'text/css' );
    $element->setAttribute( 'href', $url );
    $element->setAttribute( 'media', $media );
    $this->styles[] = $element;
  }


  public function addScript ( $url )
  {
    $element = $this->document->createElement( 'script', '' );
    $element->setAttribute( 'type', 'text/javascript' );
    $element->setAttribute( 'src', $url );
    $this->scripts[] = $element;
  }


  public function addMetaTag ( $name, $content )
  {
    $element = $this->document->createElement( 'meta' );
    $element->setAttribute( 'name', $name );
    $element->setAttribute( 'content', $content );
    $this->metas[] = $element;
  }


  public function setDescription ( $dec )
  {
    $this->addMetaTag( 'description', $dec );
  }


  public function setKeywords ( $keywords )
  {
    $this->addMetaTag( 'keywords', $keywords );
  }

  public function createElement ( $nodeName, $nodeValue=null )
  {
    return $this->document->createElement( $nodeName, $nodeValue );
  }

  public function __toString ( )
  {
    // Create the head element
    $title = $this->document->createElement( 'title', $this->title );
    $this->head->appendChild( $title );
    // Add stylesheets if needed
    if ( is_array( $this->styles ))
    foreach ( $this->styles as $element )
    $this->head->appendChild( $element );
    // Add scripts if needed
    if(  is_array( $this->scripts ))
    foreach ( $this->scripts as $element )
    $this->head->appendChild( $element );
    // Add meta tags if needed
    if ( is_array( $this->metas ))
    foreach ( $this->metas as $element )
    $this->head->appendChild( $element );
    // Create the document
    $this->document->documentElement->appendChild( $this->head );
    $this->document->documentElement->appendChild( $this->body );
    //return $this->document->saveXML( );
    //return $this->metas;

    $meta_tags = $this->metas;
    print_r($meta_tags[0]);
    
    //$meta_tags = $this->document->getElementsByTagName('meta');
    //$meta_tags = $this->head->getElementsByTagName('meta');
    //$meta_tags = $this->getElementsByTagName('meta');
    //print_r($meta_tags->length);
    
    return "Yup!";
  }

}
?>