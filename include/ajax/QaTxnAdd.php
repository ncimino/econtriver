<?php
require_once '../autoload.php';
$widget = new AjaxQaTxns($_POST['content_id']);
$widget->addEntries($_POST['acct'],$_POST['date'],$_POST['type'],$_POST['establishment'],$_POST['note'],$_POST['credit'],$_POST['debit']);
$widget->buildWidget();
?>