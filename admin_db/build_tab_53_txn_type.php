<?php
require_once '../include/autoload.php';

$sql = "CREATE TABLE txn_type
(
txn_type_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT txn_type_id PRIMARY KEY(txn_type_id),
txn_type_name varchar(255)
)";
new db_handler($sql);

$sql = "INSERT INTO txn_type (txn_type_name)
VALUES ('Deposit');";
new db_handler($sql);

$sql = "INSERT INTO txn_type (txn_type_name)
VALUES ('Withdrawl');";
new db_handler($sql);

$sql = "INSERT INTO txn_type (txn_type_name)
VALUES ('Transfer');";
new db_handler($sql);
?>