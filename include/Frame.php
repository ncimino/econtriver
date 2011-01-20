<?php
class Frame {
	protected $tabIndex;
	protected $DB;
	protected $siteInfo;
	protected $infoMsg;
	protected $user;
	protected $document;
	protected $container;
	protected $showMsgs;

	function __construct() {
		$this->showMsgs = $showMsgs;
		$this->tabIndex = new TabIndex($tabStartIndex);
		$this->DB = new DBCon();
		$this->siteInfo = new SiteInfo();
		$this->infoMsg = new InfoMsg();
		$this->user = new User($this->DB,$this->siteInfo,$this->infoMsg);
		$this->document = HTML_Document::createHTML_Document();
		$this->container = new HTML_Fragment($this->document);
	}

	function printHTML() {
		if ($this->showMsgs) $this->infoMsg->commitMessages();
		printf( '%s', Normalize::innerFragment($this->document) );
	}
	
	function addFrame($title,$id,$class) {
		$divMgmt = new HTML_Fieldset($this->container,$id);
		new HTML_Legend($divMgmt,$title,'',$class);
		$aClose = new HTML_Anchor($divMgmt,'#','','','');
		$aClose->setAttribute('onclick',"hideElement('".$id."','slow');");
		$aClose->setAttribute('title','Close');
		new HTML_Span($aClose,'','','ui-icon ui-icon-circle-close ui-state-red module_control');
		$aHideHelp = new HTML_Anchor($divMgmt,'#','','','');
		$aHideHelp->setAttribute('title','Help');
		//$aHideHelp->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		new HTML_Span($aHideHelp,'','','ui-icon ui-icon-info ui-icon-special module_control');
		return $divMgmt;
	}
}
?>