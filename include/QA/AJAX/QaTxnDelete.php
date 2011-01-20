<?php
require_once '../autoload.php';
$module = new QA_Txns($_POST['content_id']);
$module->dropEntries($_POST['txn_id'],$_POST['log']);
$module->createWidget();
?>