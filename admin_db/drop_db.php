<?php
require_once '../include/autoload.php';

try {
  $db_obj = new DBCon();
  $db_obj->query("DROP DATABASE `econtriver_db` ;");
  echo "Data base was dropped.";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>