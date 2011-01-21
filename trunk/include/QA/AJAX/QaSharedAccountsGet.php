<?php
require_once '../autoload.php';
$module = new QA_SharedAccounts($_POST['content_id']);
$module->createModule();
?>