<?php
require_once '../include/autoloader.php';

try {
  $db_obj = new DBCon();
  $sql = "CREATE TABLE user
(
user_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT user_id PRIMARY KEY(user_id),
password varchar(255) NOT NULL,
email varchar(255) NOT NULL,
timezone varchar(255) NOT NULL,
date_format varchar(255) NOT NULL,
subcat_first bit NOT NULL,
active bit NOT NULL
)";
  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>