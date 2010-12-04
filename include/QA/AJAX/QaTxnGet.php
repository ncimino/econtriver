<?php
require_once '../autoload.php';
$widget = new QA_Txns($_POST['content_id'],$_POST['sort_id'],$_POST['sort_dir'],$_POST['show_acct']);
$widget->buildWidget();
?>