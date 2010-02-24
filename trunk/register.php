<?php
require_once './include/autoload.php';
try {
  $DB = new DBCon();
  $SiteInfo = new SiteInfo();
  $User = new User($DB,$SiteInfo);
//  $Cookies = new Cookies($SiteInfo);

  $HTMLDocument = HTMLDocument::createHTMLDocument();
  $DefaultHead = new DefaultHead($HTMLDocument,$SiteInfo);
  $DefaultBody = new DefaultBody($HTMLDocument,$SiteInfo,$User,'Registration');

  $Registration = new Registration($DefaultBody->DivMid,$User);

  printf( '%s', $HTMLDocument->saveXML() );
} catch (Exception $err) {
  echo $err;
}
?>
