<?php
require_once '../autoload.php';
$widget = new QA_GroupMembers($_POST['content_id']);
$widget->dropContact($_POST['user_id']);
$widget->createWidget();
?>