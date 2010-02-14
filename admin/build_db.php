<?php
function __autoload($class_name) { require_once '../include/' .$class_name . '.php'; }

try {
  $db_obj = new DBCon(false);
  $db_obj->connect();
  $db_obj->query("CREATE DATABASE  `" . $db_obj->getDB() . "` ;");
  echo "Data base was created.";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>