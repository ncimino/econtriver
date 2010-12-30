<?php
require_once 'DB_Handler.php';
$directory = '.';
$handler = opendir($directory);
while ($file = readdir($handler)) {
	if (preg_match("/build_tab_/",$file))
	{
		echo "<h3>Require: $file </h3>\n";
		require_once $file;
		$db_obj->__destruct();
	}
}
closedir($handler);
?>