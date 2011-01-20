<?php
abstract class CORE_Module extends CORE_Container {
	protected $DB;
	protected $siteInfo;
	protected $infoMsg;
	protected $user;
	protected $showMsgs;

	function __construct($showMsgs,$tabStartIndex) {
		parent::__construct($tabStartIndex);
		$this->showMsgs = $showMsgs;
		$this->DB = new DBCon();
		$this->siteInfo = new SiteInfo();
		$this->infoMsg = new InfoMsg();
		$this->user = new User($this->DB,$this->siteInfo,$this->infoMsg);
	}

	function printHTML() {
		if ($this->showMsgs) $this->infoMsg->commitMessages();
		parent::printHTML();		
	}
}
?>