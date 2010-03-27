<?php
require_once '../include/autoload.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE txn_type
(
txn_type_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT txn_type_id PRIMARY KEY(txn_type_id),
txn_type_name varchar(255)
)";
	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";

	$sql = "INSERT INTO txn_type (txn_type_name)
VALUES ('Deposit');";
	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";

	$sql = "INSERT INTO txn_type (txn_type_name)
VALUES ('Withdrawl');";
	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";

	$sql = "INSERT INTO txn_type (txn_type_name)
VALUES ('Transfer');";
	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";

} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>