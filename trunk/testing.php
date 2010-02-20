<?php
function __autoload($class_name) { require_once './include/' .$class_name . '.php'; }

$page = new HTML();

$HTMLHead = new HTMLHead($page);
$HTMLTitle = new HTMLTitle($HTMLHead,'eContriver');
$HTMLShortcutIcon = new HTMLShortcutIcon($HTMLHead,'./images/icon_16.png','image/png');
$HTMLKeywords = new HTMLKeywords($HTMLHead,'econtriver,contriver,accounting,registry,checkbook,check,balance,savings,saving,checking,loans,loan,tracking,log,shared,accounts,account,investments,track,monitor,watch');
$HTMLDescription = new HTMLDescription($HTMLHead,'Account, investment, loan, project, and cash transaction tracking and management site with ability to share and log all account modifications');
$HTMLStylesheet = new HTMLStylesheet($HTMLHead,'./css/main.css');
$HTMLScript = new HTMLScript($HTMLHead,'','./js/main.js');
$script = "var i=10;
if (i<5)
  {
  // some code
  }";
$HTMLScript2 = new HTMLScript($HTMLHead,$script);

$HTMLBody = new HTMLBody($page);
$HTMLDiv = new HTMLDiv($HTMLBody);

/*
$something = 'ha';
$text = $page->HTMLDocument->createTextNode('/*');
$HTMLDiv->HTMLElement->appendChild($text);
$text = $page->HTMLDocument->createCDATASection('* /'.$something.'/*');
$HTMLDiv->HTMLElement->appendChild($text);
$text = $page->HTMLDocument->createTextNode('* /');
$HTMLDiv->HTMLElement->appendChild($text);
*/

$HTMLDiv2 = new HTMLDiv($HTMLDiv);
$HTMLDiv2->setAttribute( 'id', 'page_div' );
$HTMLDiv2->setAttribute( 'class', 'page' );
/*
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