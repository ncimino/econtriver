<?php
class HTMLShortcutIcon extends HTMLLink {
	function __construct($parentElement,$iconLocation,$type) {
		parent::__construct($parentElement);
		$this->setAttribute( 'rel', 'shortcut icon' );
		$this->setAttribute( 'href', $iconLocation );
		$this->setAttribute( 'type', $type );
	}
}
?>