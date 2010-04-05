<?php
require_once '../autoload.php';
$widget = new AjaxQaSharedAccounts($_POST['content_id']);
$widget->dropEntries($_POST['acct_id'],$_POST['grp_id']);
$widget->buildWidget();
?>