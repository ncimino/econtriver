<?php
require_once 'DB_Handler.php';

try {
	$db_obj = new DBCon();
	$sql = "DROP TABLE  `".QA_DB_Table::SHARE."` ;";
	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>