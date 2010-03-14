<?php
require_once '../autoload.php';
$widget = new AjaxQaAccounts($_GET['content_id']);
$widget->dropEntries($_GET['acct_id']);
$widget->buildWidget();
?>