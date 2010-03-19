<?php
require_once '../autoload.php';
$widget = new AjaxQaAccounts($_GET['content_id']);
$widget->updateEntries($_GET['name'],$_GET['acct_id']);
$widget->buildWidget();
?>