<?php
require_once '../autoload.php';
$widget = new AjaxQaTxns($_POST['content_id']);
//$widget->getTxnHistory($_POST['txn_id']);
$widget->buildTxnHistoryTable($_POST['txn_id']);
//$widget->buildWidget();
?>