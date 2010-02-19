<?php
function __autoload($class_name) { require_once './include/' .$class_name . '.php'; }

$page = new HTML();

$HTMLHead = new HTMLHead($page->HTMLDocument);
$HTMLBody = new HTMLBody($page->HTMLDocument);
$HTMLDiv = new HTMLDiv($page->HTMLDocument,$HTMLBody);
$HTMLDiv2 = new HTMLDiv($page->HTMLDocument,$HTMLDiv);
/*
$div = $page->HTMLDocument->createElement( 'div' );
$div->setAttribute( 'id', 'page_div' );
$div->setAttribute( 'class', 'page' );
$HTMLBody->HTMLElement->appendChild( $div );
//*/
printf( '%s', $page->HTMLDocument->saveXML() );

?>