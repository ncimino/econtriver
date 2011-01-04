<?php
require_once '../autoload.php';
$widget = new QA_Group_Widget($_POST['content_id']);
$widget->updateEntries($_POST['name'],$_POST['grpId']);
$widget->createWidget();
?>