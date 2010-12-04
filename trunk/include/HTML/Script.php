<?php
class HTML_Script extends HTML_Element {
	function __construct($parentElement,$content=NULL,$URL=NULL,$defer=TRUE) {
		parent::__construct($parentElement->HTML_Element->ownerDocument,$parentElement->HTML_Element,'script','');
		if(!empty($content)) {
			$textNode[0] = $parentElement->HTML_Element->ownerDocument->createTextNode("\n/*");
			$this->HTML_Element->appendChild($textNode[0]);
			$CDATASection = $parentElement->HTML_Element->ownerDocument->createCDATASection("*/\n".$content."\n/*");
			$this->HTML_Element->appendChild($CDATASection);
			$textNode[1] = $parentElement->HTML_Element->ownerDocument->createTextNode("*/\n");
			$this->HTML_Element->appendChild($textNode[1]);
		}
		$this->setAttribute( 'type' , 'text/javascript' );
		if($defer) { $this->setAttribute( 'defer', 'defer' ); }
		if(!empty($URL)) { $this->setAttribute( 'src' , $URL ); }
	}
}
?>