<?php
require_once '../autoload.php';
$widget = new QA_SharedAccounts($_POST['content_id']);
$widget->addEntries($_POST['acct_id'],$_POST['grp_id']);
$widget->buildWidget();
?>