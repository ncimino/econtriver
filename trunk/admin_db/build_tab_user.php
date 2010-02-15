<?php
require_once './include/includes.php';

$db_obj = new DBCon();

$sql = "CREATE TABLE Users
(
FirstName varchar(15),
LastName varchar(15),
Age int
)";

try {
  $myq = $db_obj->connect();
  $myq = $db_obj->query($sql);
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}

//$myq = $db_obj->query($sql);

$html_header_obj = new HTMLHeader("eContriver");
//$user_obj = new User();
//$html_login_obj = new HTMLLogin($user_obj);
$html_doc_obj = new HTMLDoc($html_header_obj->getHTMLHeader(),$html_body_str);

echo $html_doc_obj->getHTMLDoc();

?>