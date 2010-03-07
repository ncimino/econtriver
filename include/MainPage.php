<?php
class MainPage {
  function __construct($parentElement,$DB) {
    new ManageAccountsWidget($parentElement,$DB);
    //new DataEntryWidget($parentElement);
    //new ManageTagsWidget($parentElement);
    //new ManagesWidget($parentElement);
  }
}
?>