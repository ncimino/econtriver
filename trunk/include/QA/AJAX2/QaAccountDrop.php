<?php
require_once '../autoload.php';
$widget = new AjaxQaAccounts($_POST['content_id']);
$widget->dropEntries($_POST['acct_id']);
$widget->buildWidget();
?>