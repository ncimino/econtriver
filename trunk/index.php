<?php
require_once './include/autoload.php';
try {
  $DB = new DBCon();
  $SiteInfo = new SiteInfo();
  $User = new User($DB,$SiteInfo);
  $Cookies = new Cookies($SiteInfo);

  $HTMLDocument = HTMLDocument::createHTMLDocument();
  $DefaultHead = new DefaultHead($HTMLDocument,$SiteInfo);
  $DefaultBody = new DefaultBody($HTMLDocument,$SiteInfo,$User,$Cookies,'Free Multi-User Account and Investment Management');

  $H4Mid = new HTMLHeading($DefaultBody->DivMid,4,'Welcome to '.$SiteInfo->getName().'!');

  $content = "This site was created to help manage investment and account transactions.
These account tracking pages allow you share accounts and grant privileges to other 
users so that they can ";
  $TextMid = new HTMLText($DefaultBody->DivMid,$content);

  printf( '%s', $HTMLDocument->saveXML() );
} catch (Exception $err) {
  echo $err;
}
?>
