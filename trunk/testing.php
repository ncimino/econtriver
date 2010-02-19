<?php
function __autoload($class_name) { require_once './include/' .$class_name . '.php'; }

$page = new HTML();

$HTMLHead = new HTMLHead($page);
$HTMLTitle = new HTMLTitle($page,$HTMLHead,'eContriver');
$HTMLShortcutIcon = new HTMLShortcutIcon($page,$HTMLHead,'./images/icon_16.png','image/png');

$HTMLBody = new HTMLBody($page);
$HTMLDiv = new HTMLDiv($page,$HTMLBody);
$HTMLDiv2 = new HTMLDiv($page,$HTMLDiv);
$HTMLDiv2->setAttribute( 'id', 'page_div' );
$HTMLDiv2->setAttribute( 'class', 'page' );
/*
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" /> 
<meta name="Keywords" content="econtriver,contriver,accounting,registry,checkbook,check,balance,savings,saving,checking,loans,loan,tracking,log,shared,accounts,account,investments,track,monitor,watch," /> 
<meta name="Description" content="Account, investment, loan, project, and cash transaction tracking and management site with ability to share and log all account modifications" /> 
<link rel="stylesheet" type="text/css" href="./css/main.css" /> 
<script type="text/javascript" src="./js/main.js"></script> 
<!--[if lte IE 6]>
<style>
div.banner_shadow {
    display: none;
}
</style>
<![endif]-->
*/
printf( '%s', $page->HTMLDocument->saveXML() );
?>