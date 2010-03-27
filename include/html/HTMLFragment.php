<?php
class HTMLFragment extends HTMLElement {
	function __construct($HTMLDocument) {
		parent::__construct($HTMLDocument,$HTMLDocument->documentElement,'fragment','');
	}
}
?>