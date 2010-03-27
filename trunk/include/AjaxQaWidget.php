<?php
class AjaxQaWidget {
	protected $DB;
	protected $siteInfo;
	protected $infoMsg;
	protected $user;
	protected $document;
	protected $container;
	
	static function getMainClass() { return 'quick_accts'; }
	static function getQaMsgsClass() { return 'info_messages'; }
	static function getQaMsgsId() { return 'widget_info_messages'; }

	function __construct() {
		$this->DB = new DBCon();
		$this->siteInfo = new SiteInfo();
		$this->infoMsg = new InfoMsg();
		$this->user = new User($this->DB,$this->siteInfo,$this->infoMsg);
		$this->document = HTMLDocument::createHTMLDocument();
		$this->container = new HTMLFragment($this->document);
		$divInfoMsg = new HTMLDiv($this->container,self::getQaMsgsId(),self::getQaMsgsClass());
		$this->infoMsg->commitDiv($divInfoMsg);
	}

	function printHTML() {
		$this->infoMsg->commitMessages();
		printf( '%s', Normalize::innerFragment($this->document) );
	}
}
?>