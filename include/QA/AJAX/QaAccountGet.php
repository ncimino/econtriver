<?php
require_once '../autoload.php';
$widget = new QA_Accounts($_POST['content_id']);
$widget->buildWidget();
?>