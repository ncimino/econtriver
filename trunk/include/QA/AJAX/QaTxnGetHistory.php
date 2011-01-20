<?php
require_once '../autoload.php';
$module = new QA_TxnHistory($_POST['content_id'],NULL,NULL,NULL,FALSE);
$module->buildHistoryWidget($_POST['txn_id']);
?>