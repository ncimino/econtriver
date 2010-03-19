<?php
require_once '../autoload.php';
$widget = new AjaxQaSharedAccounts($_GET['content_id']);
$widget->buildWidget();
?>