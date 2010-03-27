<?php
require_once '../include/autoload.php';

try {
	$db_obj = new DBCon();

	$sql = "CREATE TABLE q_txn_notes
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT q_txn_notes_id PRIMARY KEY(id),
txn_id int NOT NULL,
CONSTRAINT q_txn_notes_txn_id FOREIGN KEY (txn_id) REFERENCES q_txn(id),
note text NOT NULL
)";

	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>