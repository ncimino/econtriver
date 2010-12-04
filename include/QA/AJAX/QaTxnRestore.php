<?php
require_once '../autoload.php';
$widget = new QA_Txns($_POST['content_id']);
$widget->restoreEntries($_POST['txn_id']);
$widget->buildWidget();
?>