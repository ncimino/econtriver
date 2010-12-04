<?php
require_once '../autoload.php';
$widget = new QA_Account_Widget($_POST['content_id']);
$widget->dropEntries($_POST['acct_id']);
$widget->buildWidget();
?>