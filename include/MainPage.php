<?php
class MainPage {
	private $focusId = '';

	const C_MENU = 'qa_menu';
	const C_MNG = 'qa_mng';
	const C_TXN = 'qa_txn';

	const I_MENU = 'qa_menu_div';
	const I_MNG = 'qa_mng_div';
	const I_TXN = 'qa_txn_div';

	function __construct($parentElement,$DB,$siteInfo,$infoMsg,$user) {
		$divMenu = new HTML_Div($parentElement,self::I_MENU,self::C_MENU);
		self::buildAccountManagementMenu($divMenu);
		new HTML_Div($parentElement,self::I_MNG,self::C_MNG);		
		new HTML_Div($parentElement,self::I_TXN,self::C_TXN);
	}
	
	function buildAccountManagementMenu($parentElement) {
		$aManageAccounts = new HTML_Anchor($parentElement,'#','Manage Accounts','acct_get',QA_Account_Build::C_CREATE);
		//$aManageAccounts->setAttribute('onclick',"QA_Account_AJAX_Get('".self::I_MNG."');");
		new HTML_Text($parentElement,' | ');
		$aManageGroups = new HTML_Anchor($parentElement,'#','Manage Groups');
		//$aManageGroups->setAttribute('onclick',"QaGroupGet('".self::I_MNG."');");
		new HTML_Text($parentElement,' | ');
		$aAccountSharing = new HTML_Anchor($parentElement,'#','Account Sharing');
		//$aAccountSharing->setAttribute('onclick',"QaSharedAccountsGet('".self::I_MNG."');");
		new HTML_Text($parentElement,' | ');
		$aGroupMembership = new HTML_Anchor($parentElement,'#','Group Membership');
		//$aGroupMembership->setAttribute('onclick',"QaGroupMembersGet('".self::I_MNG."');");
	}
}
?>