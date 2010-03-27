<?php
require_once '../include/autoload.php';

try {
	$db_obj = new DBCon(false);
	$db_obj->connect();
	$db_obj->query("CREATE DATABASE  `" . $db_obj->getDB() . "` ;");
	echo "Data base was created.";
} catch (Exception $err) {
	echo 'Caught exception: ',  $err->getMessage(), "\n";
}
?>