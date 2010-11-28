<?php
class MainPage {
	private $focusId = '';

	const qaMenu = 'quick_accounts_menu';
	const qaManage = 'quick_accounts_manage';
	const qaTxn = 'quick_accounts_txn';

	function getQaMenuClass() { return self::qaMenu; }
	function getQaMenuId() { return self::getQaMenuClass().'_div'; }
	function getQaManageClass() { return self::qaManage; }
	function getQaManageId() { return self::getQaManageClass().'_div'; }
	function getQaTxnClass() { return self::qaTxn; }
	function getQaTxnId() { return self::getQaTxnClass().'_div'; }

	function __construct($parentElement,$DB,$siteInfo,$infoMsg,$user) {

		$divMenu = new HTMLDiv($parentElement,self::getQaMenuId(),self::getQaMenuClass());
		self::buildAccountManagementMenu($divMenu);
		new HTMLDiv($parentElement,self::getQaManageId(),self::getQaManageClass());		
		new HTMLDiv($parentElement,self::getQaTxnId(),self::getQaTxnClass());
	}
	
	function buildAccountManagementMenu($parentElement) {
		$aManageAccounts = new HTML_Anchor($parentElement,'#','Manage Accounts','accounts_link');
		$aManageAccounts->setAttribute('onclick',"QaAccountGet('".self::getQaManageId()."');");
		new HTMLText($parentElement,' | ');
		$aManageGroups = new HTML_Anchor($parentElement,'#','Manage Groups');
		$aManageGroups->setAttribute('onclick',"QaGroupGet('".self::getQaManageId()."');");
		new HTMLText($parentElement,' | ');
		$aAccountSharing = new HTML_Anchor($parentElement,'#','Account Sharing');
		$aAccountSharing->setAttribute('onclick',"QaSharedAccountsGet('".self::getQaManageId()."');");
		new HTMLText($parentElement,' | ');
		$aGroupMembership = new HTML_Anchor($parentElement,'#','Group Membership');
		$aGroupMembership->setAttribute('onclick',"QaGroupMembersGet('".self::getQaManageId()."');");
	}
}
?>