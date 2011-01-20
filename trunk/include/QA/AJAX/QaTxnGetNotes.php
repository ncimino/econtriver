<?php
require_once '../autoload.php';
$module = new QA_TxnNotes($_POST['content_id'],NULL,NULL,NULL,FALSE);
$module->buildNotesWidget($_POST['txn_parent_id']);
?>