<?php
class Widget {
	/*protected $tabIndex;
	protected $DB;
	protected $siteInfo;
	protected $infoMsg;
	protected $user;
	protected $document;
	protected $container;
	protected $showMsgs;*/

	function __construct() {
		echo "1";
		/*$this->showMsgs = $showMsgs;
		$this->tabIndex = new TabIndex($tabStartIndex);
		$this->DB = new DBCon();
		$this->siteInfo = new SiteInfo();
		$this->infoMsg = new InfoMsg();
		$this->user = new User($this->DB,$this->siteInfo,$this->infoMsg);
		$this->document = HTML_Document::createHTML_Document();
		$this->container = new HTML_Fragment($this->document);*/
	}
	
	function test() {
		echo "1";
	}

	/*function printHTML() {
		if ($this->showMsgs) $this->infoMsg->commitMessages();
		printf( '%s', Normalize::innerFragment($this->document) );
	}*/
}
?>