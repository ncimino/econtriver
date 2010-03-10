<?php
require_once '../include/autoload.php';

try {
  $db_obj = new DBCon();
  $sql = "CREATE TABLE q_owners
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT q_owners_id PRIMARY KEY(id),
acct_id int NOT NULL,
CONSTRAINT q_owners_acct_id FOREIGN KEY (acct_id) REFERENCES q_acct(id),
owner_id int NOT NULL,
CONSTRAINT q_owners_owner_id FOREIGN KEY (owner_id) REFERENCES user(user_id)
)";

  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>