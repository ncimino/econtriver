<?php
require_once '../autoload.php';
$module = new QA_GroupMembers($_POST['content_id']);
$module->dropEntries($_POST['user_id'],$_POST['grp_id']);
$module->createModule();
?>