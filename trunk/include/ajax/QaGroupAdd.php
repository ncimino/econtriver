<?php
require_once '../autoload.php';
$widget = new AjaxQaGroups($_GET['content_id']);
$widget->addEntries($_GET['name']);
$widget->buildWidget();
?>