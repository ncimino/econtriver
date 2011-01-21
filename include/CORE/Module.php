<?php
class CORE_Module {
	protected $tabIndex;
	protected $DB;
	protected $siteInfo;
	protected $infoMsg;
	protected $user;
	protected $document;
	protected $showMsgs;
	public $container;

	function __construct($showMsgs,$tabStartIndex) {
		$this->showMsgs = $showMsgs;
		$this->tabIndex = new TabIndex($tabStartIndex);
		$this->DB = new DBCon();
		$this->siteInfo = new SiteInfo();
		$this->infoMsg = new InfoMsg();
		$this->user = new User($this->DB,$this->siteInfo,$this->infoMsg);
		$this->document = HTML_Document::create();
		$this->container = new HTML_Fragment($this->document);
	}

	function printHTML() {
		if ($this->showMsgs) $this->infoMsg->commitMessages();
		printf( '%s', Normalize::innerFragment($this->document) );
	}

}
?>