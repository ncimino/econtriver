<?php
require_once '../autoload.php';
$widget = new AjaxQaGroupMembers($_POST['content_id']);
$widget->dropContact($_POST['user_id']);
$widget->buildWidget();
?>