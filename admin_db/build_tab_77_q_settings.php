<?php
require_once '../include/autoload.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE q_settings
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT q_settings_id PRIMARY KEY(id),
user_id int NOT NULL,
CONSTRAINT q_settings_user_id FOREIGN KEY (user_id) REFERENCES user(user_id),
name varchar(255) NOT NULL,
CONSTRAINT q_settings_setting_user_id UNIQUE (name,user_id),
value varchar(255)
)";

	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>