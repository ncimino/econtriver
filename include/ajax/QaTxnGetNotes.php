<?php
require_once '../autoload.php';
$widget = new AjaxQaTxns($_POST['content_id'],NULL,NULL,NULL,FALSE);
$widget->buildTxnNotesTable($_POST['txn_id']);
?>