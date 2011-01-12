<?php
function __autoload($className) {
	require(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
}
?>