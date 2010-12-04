<?php
require_once '../autoload.php';
$widget = new QA_Groups($_POST['content_id']);
$widget->permDropEntries($_POST['group_id']);
$widget->buildWidget();
?>