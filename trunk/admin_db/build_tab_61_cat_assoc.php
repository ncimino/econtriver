<?php
require_once 'DB_Handler.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE cat_assoc
(
cat_assoc_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT inst_id PRIMARY KEY(cat_assoc_id),
cat_id int NOT NULL,
CONSTRAINT cat_assoc_cat_id FOREIGN KEY (cat_id) REFERENCES cat(cat_id),
acct_id int NOT NULL,
CONSTRAINT cat_assoc_acct_id FOREIGN KEY (acct_id) REFERENCES acct(acct_id)
)";
	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>