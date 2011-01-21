<?php
require_once '../autoload.php';
$module = new QA_TxnNotes($_POST['content_id']);
$module->addTxnNote($_POST['txn_id'],$_POST['note']);
$module->createModule();
?>