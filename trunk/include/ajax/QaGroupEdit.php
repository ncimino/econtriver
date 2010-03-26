<?php
require_once '../autoload.php';
$widget = new AjaxQaGroups($_POST['content_id']);
$widget->updateEntries($_POST['name'],$_POST['group_id']);
$widget->buildWidget();
?>