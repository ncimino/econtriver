<?php
function __autoload($class_name) {
	if (preg_match("/^HTML([a-zA-Z])+$/",$class_name)) {
		require_once 'html/' . $class_name . '.php';
	} elseif (preg_match("/^AjaxQa([a-zA-Z])+$/",$class_name)) {
		require_once 'qa/' . $class_name . '.php';
	}else {
		require_once $class_name . '.php';
	}
}
?>