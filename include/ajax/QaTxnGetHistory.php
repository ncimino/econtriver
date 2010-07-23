<?php
require_once '../autoload.php';
$widget = new AjaxQaTxnHistory($_POST['content_id'],NULL,NULL,NULL,FALSE);
$widget->buildHistoryWidget($_POST['txn_id']);
?>