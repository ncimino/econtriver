<?php
class HTML_ShortcutIcon extends HTML_Link {
	function __construct($parentElement,$iconLocation,$type) {
		parent::__construct($parentElement);
		$this->setAttribute( 'rel', 'shortcut icon' );
		$this->setAttribute( 'href', $iconLocation );
		$this->setAttribute( 'type', $type );
	}
}
?>