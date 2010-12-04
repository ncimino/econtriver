<?php
require_once '../autoload.php';
$widget = new QA_Group_Widget($_POST['content_id']);
$widget->dropEntries($_POST['group_id']);
$widget->createWidget();
?>