<?php
require_once 'DB_Handler.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE q_txn
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT q_txn_id PRIMARY KEY(id),
acct_id int NOT NULL,
CONSTRAINT q_txn_acct_id FOREIGN KEY (acct_id) REFERENCES q_acct(id),
user_id int NOT NULL,
CONSTRAINT q_txn_user_id FOREIGN KEY (user_id) REFERENCES user(user_id),
entered int NOT NULL,
date int NOT NULL,
type varchar(255),
establishment varchar(255),
note varchar(255),
credit decimal(38,2),
debit decimal(38,2),
parent_txn_id int,
banksays int(1),
active int(1) NOT NULL
)";
	//entered timestamp NOT NULL,

	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>