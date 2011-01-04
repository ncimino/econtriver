<?php
require_once 'DB_Handler.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE ".QA_DB_Table::SHARE."
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT ".QA_DB_Table::SHARE."_id PRIMARY KEY(id),
acct_id int NOT NULL,
CONSTRAINT ".QA_DB_Table::SHARE."_acct_id FOREIGN KEY (acct_id) REFERENCES ".QA_DB_Table::ACCT."(id),
grpId int NOT NULL,
CONSTRAINT ".QA_DB_Table::SHARE."_grpId FOREIGN KEY (grpId) REFERENCES ".QA_DB_Table::GROUP."(id),
CONSTRAINT ".QA_DB_Table::SHARE."_acct_id_grpId UNIQUE (acct_id,grpId)
)";
//active int(1) NOT NULL,

	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>