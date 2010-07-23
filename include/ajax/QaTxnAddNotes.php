<?php
require_once '../autoload.php';
$widget = new AjaxQaTxnNotes($_POST['content_id']);
$widget->addTxnNote($_POST['txn_id'],$_POST['note']);
$widget->buildWidget();
?>