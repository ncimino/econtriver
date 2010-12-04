<?php
require_once '../autoload.php';
$widget = new AjaxQaTxns($_POST['content_id']);
$widget->dropEntries($_POST['txn_id'],$_POST['log']);
$widget->buildWidget();
?>