<?php
require_once '../autoload.php';
$widget = new AjaxQaTxns($_POST['content_id']);
$widget->buildWidget();
?>