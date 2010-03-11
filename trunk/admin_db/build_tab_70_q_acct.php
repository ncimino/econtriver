<?php
require_once '../include/autoload.php';

try {
  $db_obj = new DBCon();
  $sql = "CREATE TABLE q_acct
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT q_acct_id PRIMARY KEY(id),
name varchar(255) NOT NULL,
active int(1) NOT NULL
)";

  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>