<?php
require_once '../autoload.php';
$widget = new QA_Txns($_POST['content_id']);
$widget->dropEntries($_POST['txn_id'],$_POST['log']);
$widget->buildWidget();
?>