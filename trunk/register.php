<?php
$start = microtime();
require_once './include/autoload.php';
try {
  $site = new Site('Registration');

  if ($site->user->verifyUser()) {
    new HTMLText($site->content,'You are already registered.');
  } else {
    new Registration($site->content,$site->infoMsg,$site->user);
  }

  $site->printPage();
} catch (Exception $err) { echo $err; }
echo microtime() - $start;
?>
