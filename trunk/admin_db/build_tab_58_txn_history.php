<?php
require_once 'DB_Handler.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE txn_history
(
txn_history_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT txn_history_id PRIMARY KEY(txn_history_id),
txn_id int NOT NULL,
CONSTRAINT txn_history_txn_id FOREIGN KEY (txn_id) REFERENCES txn(txn_id),
user_id int NOT NULL,
CONSTRAINT txn_history_user_id FOREIGN KEY (user_id) REFERENCES user(user_id),
acct_id int NOT NULL,
CONSTRAINT txn_history_acct_id FOREIGN KEY (acct_id) REFERENCES acct(acct_id),
txn_date int NOT NULL,
add_date int NOT NULL,
chk_no int,
memo varchar(255),
txn_type_id int NOT NULL,
CONSTRAINT txn_history_txn_type_id FOREIGN KEY (txn_type_id) REFERENCES txn_type(txn_type_id),
clear bit NOT NULL,
amount decimal(16,2) NOT NULL,
md5 VARCHAR(32) NOT NULL
)";
	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>