<?php
require_once '../autoload.php';
$widget = new AjaxQaGroups($_POST['content_id']);
$widget->buildWidget();
?>