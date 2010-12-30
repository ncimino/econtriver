<?php
require_once 'DB_Handler.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE q_user_groups
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT q_user_groups_id PRIMARY KEY(id),
group_id int NOT NULL,
CONSTRAINT q_user_groups_group_id FOREIGN KEY (group_id) REFERENCES q_group(id),
user_id int NOT NULL,
CONSTRAINT q_user_groups_user_id FOREIGN KEY (user_id) REFERENCES user(user_id),
active int(1) NOT NULL
)";

	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>