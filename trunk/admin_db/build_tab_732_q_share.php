<?php
require_once '../include/autoload.php';

try {
	$db_obj = new DBCon();
	$sql = "CREATE TABLE q_share
(
id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT q_share_id PRIMARY KEY(id),
acct_id int NOT NULL,
CONSTRAINT q_share_acct_id FOREIGN KEY (acct_id) REFERENCES q_acct(id),
group_id int NOT NULL,
CONSTRAINT q_share_group_id FOREIGN KEY (group_id) REFERENCES q_group(id),
active int(1) NOT NULL,
CONSTRAINT q_share_acct_id_group_id UNIQUE (acct_id,group_id)
)";

	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>