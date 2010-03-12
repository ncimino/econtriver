<?php
class MainPage {
  private $focusId = '';
  
  function __construct($parentElement,$DB,$siteInfo,$infoMsg,$user) {
    
    $divMenu = new HTMLDiv($parentElement,'quick_accounts_menu');
    
    $aManageAccounts = new HTMLAnchor($divMenu,'#','Manage Accounts');
    $ManageQuickAccountsWidget = new ManageQuickAccountsWidget($parentElement,$DB,$siteInfo,$infoMsg,$user);
    //*
    new HTMLText($divMenu,' | ');
    $aManageGroups = new HTMLAnchor($divMenu,'#','Manage Groups');
    $ManageQuickGroupsWidget = new ManageQuickGroupsWidget($parentElement,$DB,$siteInfo,$infoMsg,$user);

    $aManageGroups->setAttribute('onclick',"Effect.toggle('{$ManageQuickGroupsWidget->getContainerId()}','blind');Effect.BlindUp('{$ManageQuickAccountsWidget->getContainerId()}'); return false;");
    $aManageAccounts->setAttribute('onclick',"Effect.toggle('{$ManageQuickAccountsWidget->getContainerId()}','blind');Effect.BlindUp('{$ManageQuickGroupsWidget->getContainerId()}'); return false;");
    /*
    new HTMLText($divMenu,' | ');
    $aManageShares = new HTMLAnchor($divMenu,'#','Account Sharing');
    $ManageQuickSharesWidget = new ManageQuickSharesWidget($parentElement,$DB,$siteInfo,$infoMsg,$user);
    $aManageShares->setAttribute('onclick',"Effect.toggle('{$ManageQuickSharesWidget->getContainerId()}','blind'); return false;");
    //*/
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