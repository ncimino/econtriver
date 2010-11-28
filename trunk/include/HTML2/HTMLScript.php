<?php
class HTMLScript extends HTMLElement {
	function __construct($parentElement,$content=NULL,$URL=NULL,$defer=TRUE) {
		parent::__construct($parentElement->HTMLElement->ownerDocument,$parentElement->HTMLElement,'script','');
		if(!empty($content)) {
			$textNode[0] = $parentElement->HTMLElement->ownerDocument->createTextNode("\n/*");
			$this->HTMLElement->appendChild($textNode[0]);
			$CDATASection = $parentElement->HTMLElement->ownerDocument->createCDATASection("*/\n".$content."\n/*");
			$this->HTMLElement->appendChild($CDATASection);
			$textNode[1] = $parentElement->HTMLElement->ownerDocument->createTextNode("*/\n");
			$this->HTMLElement->appendChild($textNode[1]);
		}
		$this->setAttribute( 'type' , 'text/javascript' );
		if($defer) { $this->setAttribute( 'defer', 'defer' ); }
		if(!empty($URL)) { $this->setAttribute( 'src' , $URL ); }
	}
}
?>