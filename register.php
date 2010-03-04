<?php
$starttime = microtime();
$startarray = explode(" ", $starttime);
$starttime = $startarray[1] + $startarray[0];

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

$endtime = microtime();
$endarray = explode(" ", $endtime);
$endtime = $endarray[1] + $endarray[0];
$totaltime = $endtime - $starttime; 
$totaltime = round($totaltime,5);
echo "<br/>This page loaded in $totaltime seconds.";
?>
