<?php
class HTML_Keywords extends HTML_Meta {
	function __construct($parentElement,$content) {
		parent::__construct($parentElement,$parentElement);
		$this->setAttribute( 'name', 'keywords' );
		$this->setAttribute( 'content', $content );
	}
}
?>