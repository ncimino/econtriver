<?php
abstract class QA_Module extends ModuleCore {
	protected $parentId;
	
	const C_FRAME = 'module_frame';
	const C_MAIN = 'quick_accts';
	const C_MGMT = 'manage_title';
	const C_MSGS = 'info_messages';
	
	const I_FS = 'qa_id';
	const I_FS_CLOSE = 'qa_close_id';
	const I_MSGS = 'module_info_messages';
	
	function __construct($parentId,$showMsgs=TRUE,$tabStartIndex=0) {
		parent::__construct($showMsgs,$tabStartIndex);
		$this->parentId = $parentId;
		if ($this->showMsgs) {
			$divMsgs = new HTML_Div($this->container,self::I_MSGS,self::C_MSGS);
			$this->infoMsg->commitDiv($divMsgs);
		}
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
	}
	
	function addFrame($title) {
		return parent::addFrame($title,self::I_FS,self::C_MGMT);
	}
}
?>