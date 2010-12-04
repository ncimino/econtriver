<?php
function __autoload($class_name) {
	require(str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php');
}
?>