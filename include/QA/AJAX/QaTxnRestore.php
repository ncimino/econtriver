<?php
require_once '../autoload.php';
$module = new QA_Txns($_POST['content_id']);
$module->restoreEntries($_POST['txn_id']);
$module->createModule();
?>