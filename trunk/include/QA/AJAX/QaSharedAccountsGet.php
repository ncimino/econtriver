<?php
require_once '../autoload.php';
$widget = new QA_SharedAccounts($_POST['content_id']);
$widget->createWidget();
?>