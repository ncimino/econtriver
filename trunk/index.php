<?php
$starttime = microtime();
$startarray = explode(" ", $starttime);
$starttime = $startarray[1] + $startarray[0];

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

$endtime = microtime();
$endarray = explode(" ", $endtime);
$endtime = $endarray[1] + $endarray[0];
$totaltime = $endtime - $starttime; 
$totaltime = round($totaltime,5);
echo "<br/>This page loaded in $totaltime seconds.";
?>
