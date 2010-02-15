<?php
require_once '../include/autoloader.php';

try {
  $db_obj = new DBCon();
  $sql = "CREATE TABLE acct
(
acct_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT acct_id PRIMARY KEY(acct_id),
acct_name varchar(255) NOT NULL,
acct_type_id int NOT NULL,
CONSTRAINT acct_acct_type_id FOREIGN KEY (acct_type_id) REFERENCES acct_type(acct_type_id),
inst_id int NOT NULL,
CONSTRAINT acct_inst_id FOREIGN KEY (inst_id) REFERENCES inst(inst_id),
active bit NOT NULL
)";
  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>