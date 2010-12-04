<?php
require_once '../autoload.php';
$widget = new AjaxQaSharedAccounts($_POST['content_id']);
$widget->buildWidget();
?>