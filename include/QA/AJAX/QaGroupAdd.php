<?php
require_once '../autoload.php';
$widget = new QA_Group_Widget($_POST['content_id']);
$widget->addEntries($_POST['name']);
$widget->createWidget();
?>