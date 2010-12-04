<?php
require_once '../autoload.php';
$widget = new QA_GroupMembers($_POST['content_id']);
$widget->dropEntries($_POST['user_id'],$_POST['grp_id']);
$widget->createWidget();
?>