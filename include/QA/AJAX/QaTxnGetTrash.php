<?php
require_once '../autoload.php';
$module = new QA_TxnTrash($_POST['content_id'],$_POST['sort_id'],$_POST['sort_dir'],$_POST['show_acct'],FALSE);
$module->buildTrashModule();
?>