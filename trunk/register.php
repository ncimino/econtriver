<?php
require_once './include/autoload.php';
try {
  $HTMLDocument = HTMLDocument::createHTMLDocument();

  $DB = new DBCon();
  $siteInfo = new SiteInfo();
  $user = new User($DB,$siteInfo);

  $DefaultHead = new DefaultHead($HTMLDocument,$siteInfo);
  $DefaultBody = new DefaultBody($HTMLDocument,$siteInfo,$user,'Registration');

  if ($user->verifyUser()) {
    new HTMLHeading($DefaultBody->DivMid,4,'You are already registered.');
  } else {
    $Registration = new Registration($DefaultBody->DivMid,$user);
  }

  printf( '%s', $HTMLDocument->saveXML() );
} catch (Exception $err) {
  echo $err;
}
?>
