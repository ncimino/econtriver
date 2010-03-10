<?php
require_once '../include/autoload.php';

try {
  $db_obj = new DBCon();
  $sql = "DROP TABLE  `q_group` ;";
  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>