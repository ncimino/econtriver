<?php
require_once 'DB_Handler.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE ".QA_DB_Table::USER_GROUPS."
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT ".QA_DB_Table::USER_GROUPS."_id PRIMARY KEY(id),
grpId int NOT NULL,
CONSTRAINT ".QA_DB_Table::USER_GROUPS."_grpId FOREIGN KEY (grpId) REFERENCES ".QA_DB_Table::GROUP."(id),
user_id int NOT NULL,
CONSTRAINT ".QA_DB_Table::USER_GROUPS."_user_id FOREIGN KEY (user_id) REFERENCES user(user_id),
active int(1) NOT NULL
)";

	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>