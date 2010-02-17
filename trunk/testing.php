<?php
require_once './include/autoloader.php';

$db_obj = new DBCon();
$site_info_obj = new SiteInfo();
$user_obj = new User($db_obj);

$doc = new HTMLDocument();

//$doc->title = 'eContriver';
//$doc->addStyleSheet( 'css/main.css' );
//$doc->addScript( 'js/main.js' );

$div1 = $doc->createElement( 'div' );
$div1->setAttribute( 'class', 'page' );
$doc->body->appendChild( $div1 );

$div2 = $doc->createElement( 'div' );
$div2->setAttribute( 'class', 'banner_ie_limiter' );
$div1->appendChild( $div2 );

$div3 = $doc->createElement( 'div' );
$div3->setAttribute( 'class', 'banner' );
$div3->nodeValue .= new HTMLLogin($user_obj);
$div3->nodeValue .= new HTMLLogo($site_info_obj);
$div1->appendChild( $div3 );

$div4 = $doc->createElement( 'div' );
$div4->setAttribute( 'class', 'mid' );
$div1->appendChild( $div4 );

$h3_1 = $doc->createElement( 'h3' );
$h3_1->nodeValue .= "Free Multi-User Account and Investment Management";
$div4->appendChild( $h3_1 );

$a_1 = $doc->createElement( 'a' );
$a_1->setAttribute( 'href', 'testing.php' );
$a_1->nodeValue .= "testing";
$div4->appendChild( $a_1 );

$doc->addMetaTag ( "Content-Type", "text/html; charset=ISO-8859-1" );


printf( '%s', $doc );
//print_r ($doc);

?>