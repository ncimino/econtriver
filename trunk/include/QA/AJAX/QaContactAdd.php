<?php
require_once '../autoload.php';
$widget = new QA_GroupMembers($_POST['content_id']);
$widget->addContact($_POST['name']);
$widget->buildWidget();
?>