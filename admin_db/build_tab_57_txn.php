<?php
require_once '../include/autoload.php';

try {
  $db_obj = new DBCon();
  $sql = "CREATE TABLE txn
(
txn_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT txn_id PRIMARY KEY(txn_id),
user_id int NOT NULL,
CONSTRAINT txn_user_id FOREIGN KEY (user_id) REFERENCES user(user_id),
acct_id int NOT NULL,
CONSTRAINT txn_acct_id FOREIGN KEY (acct_id) REFERENCES acct(acct_id),
txn_date int NOT NULL,
add_date int NOT NULL,
chk_no int,
memo varchar(255),
txn_type_id int NOT NULL,
CONSTRAINT txn_txn_type_id FOREIGN KEY (txn_type_id) REFERENCES txn_type(txn_type_id),
clear bit NOT NULL,
amount decimal(16,2) NOT NULL,
md5 VARCHAR(32) NOT NULL,
active bit NOT NULL
)";
  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>