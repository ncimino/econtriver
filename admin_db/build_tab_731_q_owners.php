<?php
require_once 'DB_Handler.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE ".QA_DB_Table::OWNERS."
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT ".QA_DB_Table::OWNERS."_id PRIMARY KEY(id),
acct_id int NOT NULL,
CONSTRAINT ".QA_DB_Table::OWNERS."_acct_id FOREIGN KEY (acct_id) REFERENCES ".QA_DB_Table::ACCT."(id),
owner_id int NOT NULL,
CONSTRAINT ".QA_DB_Table::OWNERS."_owner_id FOREIGN KEY (owner_id) REFERENCES user(user_id)
)";

	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>