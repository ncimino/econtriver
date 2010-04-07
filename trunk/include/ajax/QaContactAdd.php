<?php
require_once '../autoload.php';
$widget = new AjaxQaGroupMembers($_POST['content_id']);
$widget->addContact($_POST['name']);
$widget->buildWidget();
?>