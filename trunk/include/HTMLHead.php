<?php
class HTMLHead
{
  public $HTMLElement;
  function __construct($HTMLDocument) {
    $this->HTMLElement = $HTMLDocument->createElement( 'head', '' );
    $HTMLDocument->documentElement->appendChild( $this->HTMLElement );
  }
  /*
  public function __construct($document)
  {
    $head = $document->document->createElement( 'head', '' );
    $document->document->documentElement->appendChild( $head );
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
  //*/

}
?>