<?php
require_once '../autoload.php';
$widget = new AjaxQaGroups($_GET['content_id']);
$widget->updateEntries($_GET['name'],$_GET['group_id']);
$widget->buildWidget();
?>