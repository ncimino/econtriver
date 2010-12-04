<?php
require_once '../autoload.php';
$widget = new QA_GroupMembers($_POST['content_id']);
$widget->addEntries($_POST['user_id'],$_POST['grp_id']);
$widget->buildWidget();
?>