<?php
require_once './include/autoload.php';
try {
  $site = new Site('Free Multi-User Account and Investment Management');

  new HTMLHeading($site->content,4,'Welcome to '.$site->siteInfo->getName().'!');
  $content = "This site was created to help manage investment and account transactions.
These account tracking pages allow you share accounts and grant privileges to other 
users so that they can add, remove, and change ";
  new HTMLText($site->content,$content);
  
  $site->printPage();
} catch (Exception $err) { echo $err; }
?>
