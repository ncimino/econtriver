<?php
require_once '../autoload.php';
$widget = new AjaxQaAccounts($_POST['content_id']);
$widget->addEntries($_POST['name']);
$widget->buildWidget();
?>