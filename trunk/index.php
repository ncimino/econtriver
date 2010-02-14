<?php
function __autoload($class_name) { require_once './include/' .$class_name . '.php'; }

$db_obj = new DBCon();
$html_header_obj = new HTMLHeader("eContriver");
//$user_obj = new User();
//$html_login_obj = new HTMLLogin($user_obj);
$html_doc_obj = new HTMLDoc($html_header_obj->getHTMLHeader(),$html_body_str);

echo $html_doc_obj->getHTMLDoc();

?>