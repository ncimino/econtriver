<?php
require_once '../include/autoloader.php';

try {
  $db_obj = new DBCon();
  $sql = "CREATE TABLE tag
(
tag_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT tag_id PRIMARY KEY(tag_id),
tag_name varchar(255)
)";
  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>