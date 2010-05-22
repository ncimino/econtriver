<?php
require_once '../autoload.php';
$widget = new AjaxQaTxns($_POST['content_id']);
$widget->addEntries($_POST['acct'],$_POST['date'],$_POST['type'],$_POST['establishment'],$_POST['note'],$_POST['credit'],$_POST['debit'],
					$_POST['parent_id'],$_POST['banksays'],$_POST['current_txn_id']);
$widget->buildWidget();
?>