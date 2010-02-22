<?php
require_once './include/autoload.php';

$DB = new DBCon();
$SiteInfo = new SiteInfo();
$User = new User($DB);

$HTMLDocument = HTMLDocument::createHTMLDocument();
$DefaultHead = new DefaultHead($HTMLDocument,$SiteInfo);
$DefaultBody = new DefaultBody($HTMLDocument,$SiteInfo,$User,'Register');

$Registration = new Registration();

$TextMid = new HTMLText($DefaultBody->DivMid,$content);

printf( '%s', $HTMLDocument->saveXML() );
?>
