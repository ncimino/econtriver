<?php
require_once '../include/autoload.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE q_txn
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT q_txn_id PRIMARY KEY(id),
entered timestamp NOT NULL,
date int NOT NULL,
description varchar(255),
credit decimal(38,2),
debit decimal(38,2),
attachment mediumblob,
active int(1) NOT NULL
)";

	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>