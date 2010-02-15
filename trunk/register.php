<?php
require_once './include/autoloader.php';

$db_obj = new DBCon();
$html_header_obj = new HTMLHeader("Register");
$user_obj = new User($db_obj);
$html_login_obj = new HTMLLogin($user_obj);
$html_body_str = $html_login_obj->getHTMLLogin();
$html_doc_obj = new HTMLDoc($html_header_obj->getHTMLHeader(),$html_body_str);

echo $html_doc_obj->getHTMLDoc();

?>