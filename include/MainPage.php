<?php
class MainPage {
  private $focusId = '';
  
  function __construct($parentElement,$DB,$siteInfo,$infoMsg,$user) {
    
    $divMenu = new HTMLDiv($parentElement,'quick_accounts_menu');
    
    $aManageAccounts = new HTMLAnchor($divMenu,'#','Manage Accounts');
    $ManageQuickAccountsWidget = new ManageQuickAccountsWidget($parentElement,$DB,$siteInfo,$infoMsg,$user);
    $aManageAccounts->setAttribute('onclick',"Effect.toggle('{$ManageQuickAccountsWidget->getContainerId()}','blind'); return false;");
    
    new HTMLText($divMenu,' | ');
    
    $aManageGroups = new HTMLAnchor($divMenu,'#','Manage Groups');
    $ManageQuickGroupsWidget = new ManageQuickGroupsWidget($parentElement,$DB,$siteInfo,$infoMsg,$user);
    $aManageGroups->setAttribute('onclick',"Effect.toggle('{$ManageQuickGroupsWidget->getContainerId()}','blind'); return false;");
    
    new HTMLText($divMenu,' | ');
    
    $ManageQuickSharesWidget = new ManageQuickSharesWidget($parentElement,$DB,$siteInfo,$infoMsg,$user);

    $this->focusId = $ManageQuickAccountsWidget->getFocusId();
    if(empty($this->focusId)) { $this->focusId = $ManageQuickGroupsWidget->getFocusId(); }
    if(empty($this->focusId)) { $this->focusId = $ManageQuickSharesWidget->getFocusId(); }

    //new ManageAccountsWidget($parentElement,$DB,$siteInfo);
    //new DataEntryWidget($parentElement);
    //new ManageTagsWidget($parentElement);
    //new ManagesWidget($parentElement);

    if (!empty($this->focusId)) {
      new HTMLScript($parentElement,"document.getElementById(\"" . $this->focusId . "\").focus();");
    }
  }
}
?>