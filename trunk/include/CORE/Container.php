<?php
abstract class CORE_Container {
	protected $tabIndex;
	protected $document;
	protected $container;

	function __construct($tabStartIndex=0) {
		$this->tabIndex = new TabIndex($tabStartIndex);
		$this->document = HTML_Document::create();
		$this->container = new HTML_Fragment($this->document);
	}

	function printHTML() {
		printf( '%s', Normalize::innerFragment($this->document) );
	}
}
?>