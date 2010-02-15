<?php
require_once '../include/autoloader.php';

try {
  $db_obj = new DBCon();
  $sql = "CREATE TABLE tag_assoc
(
tag_assoc_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT tag_assoc_id PRIMARY KEY(tag_assoc_id),
tag_id int NOT NULL,
CONSTRAINT tag_assoc_tag_id FOREIGN KEY (tag_id) REFERENCES tag(tag_id),
txn_id int NOT NULL,
CONSTRAINT tag_assoc_txn_id FOREIGN KEY (txn_id) REFERENCES txn(txn_id),
acct_id int NOT NULL,
CONSTRAINT tag_assoc_acct_id FOREIGN KEY (acct_id) REFERENCES acct(acct_id)
)";
  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>