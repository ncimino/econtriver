<?php
require_once 'DB_Handler.php';
try {
	$db_obj = new DBCon(false);
	$sql="CREATE DATABASE  `" . $db_obj->getDB() . "`";
	$db_obj->query($sql);
	echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $e) { new ExceptionHandler($e); }
?>