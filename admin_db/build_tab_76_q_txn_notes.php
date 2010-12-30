<?php
require_once 'DB_Handler.php';

try {
	$db_obj = new DBCon();

	$sql = "CREATE TABLE q_txn_notes
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT q_txn_notes_id PRIMARY KEY(id),
user_id int NOT NULL,
CONSTRAINT q_txn_notes_user_id FOREIGN KEY (user_id) REFERENCES user(user_id),
txn_id int NOT NULL,
CONSTRAINT q_txn_notes_txn_id FOREIGN KEY (txn_id) REFERENCES q_txn(id),
posted int NOT NULL,
edited int,
note text NOT NULL
)";

	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>