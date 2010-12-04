<?php
class Head {
	public $HTML_Head;
	function __construct($HTML_Document,$siteInfo) {
		$this->HTML_Head = new HTML_Head($HTML_Document);
		new HTML_Title($this->HTML_Head,$siteInfo->getName());
		new HTML_ShortcutIcon($this->HTML_Head,$siteInfo->getIcon(),$siteInfo->getIconType());
		new HTML_Keywords($this->HTML_Head,$siteInfo->getKeywords());
		new HTML_Description($this->HTML_Head,$siteInfo->getDescription());
		foreach ($siteInfo->getCss() as $cssFile) {	new HTML_Stylesheet($this->HTML_Head,$cssFile); }
		foreach ($siteInfo->getJs() as $jsFile) { new HTML_Script($this->HTML_Head,'',$jsFile); }
	}
}
?>