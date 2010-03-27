<?php
require_once '../include/autoload.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE acct_type
(
acct_type_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT acct_type_id PRIMARY KEY(acct_type_id),
acct_type_name varchar(255)
)";
	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>