<?php
function __autoload($class_name) { require_once '../include/' .$class_name . '.php'; }

try {
  $db_obj = new DBCon();
  $db_obj->query("DROP DATABASE `econtriver_db` ;");
  echo "Data base was dropped.";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>