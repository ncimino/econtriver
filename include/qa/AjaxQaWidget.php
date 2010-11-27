<?php
abstract class AjaxQaWidget {
	protected $tabIndex;
	protected $DB;
	protected $siteInfo;
	protected $infoMsg;
	protected $user;
	protected $document;
	protected $container;
	protected $show_msg_div;

	static function getMainClass() { return 'quick_accts'; }
	static function getQaMsgsClass() { return 'info_messages'; }
	static function getQaMsgsId() { return 'widget_info_messages'; }

	function __construct($show_msg_div=TRUE,$tabStartIndex=0) {
		$this->show_msg_div = $show_msg_div;
		$this->tabIndex = new TabIndex($tabStartIndex);
		$this->DB = new DBCon();
		$this->siteInfo = new SiteInfo();
		$this->infoMsg = new InfoMsg();
		$this->user = new User($this->DB,$this->siteInfo,$this->infoMsg);
		$this->document = HTMLDocument::createHTMLDocument();
		$this->container = new HTMLFragment($this->document);
		if ($this->show_msg_div) {
			$divInfoMsg = new HTMLDiv($this->container,self::getQaMsgsId(),self::getQaMsgsClass());
			$this->infoMsg->commitDiv($divInfoMsg);
		}
	}

	function printHTML() {
		if ($this->show_msg_div) $this->infoMsg->commitMessages();
		printf( '%s', Normalize::innerFragment($this->document) );
	}
}
?>