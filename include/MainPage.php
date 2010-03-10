<?php
class MainPage {
  function __construct($parentElement,$DB,$siteInfo,$infoMsg,$user) {
    new ManageQuickAccountsWidget($parentElement,$DB,$siteInfo,$infoMsg,$user);

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