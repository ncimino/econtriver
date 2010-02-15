<?php
require_once '../include/autoloader.php';

try {
  $db_obj = new DBCon();
  $sql = "CREATE TABLE inst
(
inst_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT inst_id PRIMARY KEY(inst_id),
inst_name varchar(255)
)";
  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>