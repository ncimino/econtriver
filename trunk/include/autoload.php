<?php
function __autoload($className) {
	echo "Loading:" . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php<br>';
	require(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
}
?>