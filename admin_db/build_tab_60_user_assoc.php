<?php
require_once 'DB_Handler.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE user_assoc
(
user_assoc_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT user_assoc_id PRIMARY KEY(user_assoc_id),
user_id int NOT NULL,
CONSTRAINT user_assoc_user_id FOREIGN KEY (user_id) REFERENCES user(user_id),
acct_id int NOT NULL,
CONSTRAINT user_assoc_acct_id FOREIGN KEY (acct_id) REFERENCES acct(acct_id)
)";
	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>