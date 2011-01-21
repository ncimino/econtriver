<?php
class Frame {
	protected $tabIndex;
	protected $fieldsetId;
	protected $legendClass;
	protected $title;
	protected $container;

	function __construct($parentElement,$title,$fieldsetId,$legendClass=0,$tabStartIndex=0) {
		$this->container = new HTML_Fieldset($parentElement);
		$this->tabIndex = new TabIndex($tabStartIndex);
		$this->title = $title;
		$this->fieldsetId = $fieldsetId;
		$this->legendClass = $legendClass;
	}
	
	function getContainer() {
		return $this->container;
	}
	
	function build() {
		$this->container->setId($this->fieldsetId);
		new HTML_Legend($this->container,$this->title,'',$this->legendClass);
		new HTML_Span($this->container,'','','ui-icon ui-icon-circle-close ui-state-red module_control');
		new HTML_Span($this->container,'','','ui-icon ui-icon-info ui-icon-special module_control');
	}
}
?>