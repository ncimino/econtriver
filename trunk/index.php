<?php
require_once './include/autoload.php';

$site = new Site('Main Page');

if ($site->user->verifyUser()) {
  $site->body->title->remove();
  new MainPage($site->content,$site->DB);
} else {
  $site->landingPage();
}

$site->printPage();
?>
