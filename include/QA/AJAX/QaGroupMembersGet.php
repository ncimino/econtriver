<?php
require_once '../autoload.php';
$module = new QA_GroupMembers($_POST['content_id']);
$module->createModule();
?>