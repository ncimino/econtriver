<?php
class QA_Module extends CORE_Module {
	const C_FRAME = 'module_frame';
	const C_MAIN = 'quick_accts';
	const C_MGMT = 'manage_title';
	const C_MSGS = 'info_msgs';
	
	const I_FS = 'qa_id';
	const I_FS_CLOSE = 'qa_close_id';
	const I_MSGS = 'module_msgs';
	
	function __construct($showMsgs=TRUE,$tabStartIndex=0) {
		parent::__construct($showMsgs,$tabStartIndex);
		if ($this->showMsgs) {
			$divMsgs = new HTML_Div($this->container,self::I_MSGS,self::C_MSGS);
			$this->infoMsg->commitDiv($divMsgs);
		}
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
	}
	
}
?>