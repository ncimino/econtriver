<?php
class QA_Widget extends Widget/**/ {
	private $parentId;
	
	const C_FRAME = 'widget_frame';
	const C_MAIN = 'quick_accts';
	const C_MGMT = 'manage_title';
	const C_MSGS = 'info_messages';
	
	const I_FS = 'qa_id';
	const I_FS_CLOSE = 'qa_close_id';
	const I_MSGS = 'widget_info_messages';
	
	function __construct($parentId,$showMsgs=TRUE,$tabStartIndex=0) {
		/*
		parent::__construct($showMsgs,$tabStartIndex);
		$this->parentId = $parentId;
		if ($this->showMsgs) {
			$divMsgs = new HTML_Div($this->container,self::I_MSGS,self::C_MSGS);
			$this->infoMsg->commitDiv($divMsgs);
		}
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}*/
	}
	
	function addMgmtFrame($title) {
		$divMgmt = new HTML_Fieldset($this->parentId,self::I_FS);
		new HTML_Legend($divMgmt,$title,'',self::C_MGMT);
		$aClose = new HTML_Anchor($divMgmt,'#','','','');
		$aClose->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		$aClose->setAttribute('title','Close');
		new HTML_Span($aClose,'','','ui-icon ui-icon-circle-close ui-state-red widget_control');
		$aHideHelp = new HTML_Anchor($divMgmt,'#','','','');
		$aHideHelp->setAttribute('title','Help');
		//$aHideHelp->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		new HTML_Span($aHideHelp,'','','ui-icon ui-icon-info ui-icon-special widget_control');
	}
}
?>