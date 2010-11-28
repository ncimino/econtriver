<?php
class HTMLHead extends HTMLElement {
	function __construct($HTMLDocument) {
		parent::__construct($HTMLDocument,$HTMLDocument->documentElement,'head','');
	}
}
?>