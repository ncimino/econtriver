<?php
class HTML_Body extends HTML_Element {
	function __construct($HTML_Document) {
		parent::__construct($HTML_Document,$HTML_Document->documentElement,'body','');
	}
}
?>