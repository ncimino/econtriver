<?php
require_once '../autoload.php';
$widget = new QA_Account_Widget($_POST['content_id']);
$widget->updateEntries($_POST['name'],$_POST['acct_id']);
$widget->createWidget();
?>