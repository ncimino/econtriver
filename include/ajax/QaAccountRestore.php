<?php
require_once '../autoload.php';
$widget = new AjaxQaAccounts($_POST['content_id']);
$widget->restoreEntries($_POST['acct_id']);
$widget->buildWidget();
?>