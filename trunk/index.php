<?php
require_once './include/autoloader.php';

$db_obj = new DBCon();
$site_info_obj = new SiteInfo();
$user_obj = new User($db_obj);

$header_arr[] = new HTMLHeader('');
$body_arr[] = "<div class=\"banner_ie_limiter\" ></div>\n";
$body_arr[] = "<div class=\"banner\" >\n";
$body_arr[] = new HTMLLogin($user_obj);
$body_arr[] = new HTMLLogo($site_info_obj);
$body_arr[] = "</div>\n";
$body_arr[] = "<div class=\"mid\" >\n";
$body_arr[] = "<h3>Free Multi-User Account Registry</h3>\n";
$body_arr[] = "</div>\n";
$html_doc_obj = new HTMLDoc($header_arr,$body_arr);


echo $html_doc_obj;

?>