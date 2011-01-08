<?php
class QA_Module extends QA_Widget {
	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		
		$divQuickAccounts = new HTML_Fieldset($this->parentId,self::I_FS);
		new HTML_Legend($divQuickAccounts,'Account Management',NULL,'manage_title');
		$aClose = new HTML_Anchor($divQuickAccounts,'#','','','');
		$aClose->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		$aClose->setAttribute('title','Close');
		new HTML_Span($aClose,'','','ui-icon ui-icon-circle-close ui-state-red widget_control');
		$aHideHelp = new HTML_Anchor($divQuickAccounts,'#','','','');
		$aHideHelp->setAttribute('title','Help');
		//$aHideHelp->setAttribute('onclick',"hideElement('".self::I_FS."','slow');");
		new HTML_Span($aHideHelp,'','','ui-icon ui-icon-info ui-icon-special widget_control');
		
	}
	
	
}
?>