<?php
require_once 'DB_Handler.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE ".QA_DB_Table::ACCT."
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT ".QA_DB_Table::ACCT."_id PRIMARY KEY(id),
name varchar(255) NOT NULL,
active int(1) NOT NULL
)";

	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>