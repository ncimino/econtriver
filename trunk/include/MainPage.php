<?php
class MainPage {
  private $focusId = '';
  
  function __construct($parentElement,$DB,$siteInfo,$infoMsg,$user) {
    $ManageQuickAccountsWidget = new ManageQuickAccountsWidget($parentElement,$DB,$siteInfo,$infoMsg,$user);
    $ManageQuickGroupsWidget = new ManageQuickGroupsWidget($parentElement,$DB,$siteInfo,$infoMsg,$user);

    $this->focusId = $ManageQuickAccountsWidget->getFocusId();
    if(empty($this->focusId)) { $this->focusId = $ManageQuickGroupsWidget->getFocusId(); }

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