<?php
require_once './include/autoload.php';

$site = new Site('Quick Accounts');

if ($site->user->verifyUser()) {
  //$site->body->title->remove();
  new MainPage($site->content,$site->DB,$site->siteInfo,$site->infoMsg,$site->user);
} else {
  $site->landingPage();
}

$site->printPage();
?>
