<?php
require_once '../autoload.php';
$module = new QA_SharedAccounts($_POST['content_id']);
$module->dropEntries($_POST['acct_id'],$_POST['grp_id']);
$module->createWidget();
?>