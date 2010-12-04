<?php
class HTML_Description extends HTML_Meta {
	function __construct($parentElement,$content) {
		parent::__construct($parentElement);
		$this->setAttribute( 'name', 'description' );
		$this->setAttribute( 'content', $content );
	}
}
?>