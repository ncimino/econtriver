<?php
require_once 'DB_Handler.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE contacts
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT contacts_id PRIMARY KEY(id),
owner_id int NOT NULL,
CONSTRAINT contacts_owner_id FOREIGN KEY (owner_id) REFERENCES user(user_id),
contact_id int NOT NULL,
CONSTRAINT contacts_contact_id FOREIGN KEY (contact_id) REFERENCES user(user_id),
CONSTRAINT contacts_owner_id_contact_id UNIQUE (owner_id,contact_id)
)";
	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>