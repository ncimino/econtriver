<?php
class HTML_Stylesheet extends HTML_Link {
	function __construct($parentElement,$iconLocation) {
		parent::__construct($parentElement);
		$this->setAttribute( 'rel', 'stylesheet' );
		$this->setAttribute( 'type', 'text/css' );
		$this->setAttribute( 'href', $iconLocation );
	}
}
?>