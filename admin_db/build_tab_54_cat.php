<?php
require_once '../include/autoload.php';

try {
  $db_obj = new DBCon();
  $sql = "CREATE TABLE cat
(
cat_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT inst_id PRIMARY KEY(cat_id),
cat_name varchar(255),
user_id int NOT NULL,
CONSTRAINT cat_user_id FOREIGN KEY (user_id) REFERENCES user(user_id)
)";
  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>