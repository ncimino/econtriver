<?php
require_once '../include/autoload.php';

$DB = new DBCon();
$siteInfo = new SiteInfo();
$infoMsg = new InfoMsg();
$user = new User($DB,$siteInfo,$infoMsg);

$emailDocument = new EmailDocument($siteInfo,$user,'Just Testing');
new HTML_Text($emailDocument->content,'This is where the page content will go.');

printf('%s',$emailDocument->printPage());

?>