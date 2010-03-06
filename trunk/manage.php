<?php
require_once './include/autoload.php';
try {
  $site = new Site('Account Management');

  if (!$site->user->verifyUser()) {
    new HTMLText($site->content,'You must be logged in to manage your account.');
  } else {
    new ManageAccount($site->content,$site->infoMsg,$site->siteInfo,$site->user,$site->body);
  }

  $site->printPage();
} catch (Exception $err) { echo $err; }
?>
