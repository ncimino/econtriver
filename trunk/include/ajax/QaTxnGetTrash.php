<?php
require_once '../autoload.php';
$widget = new AjaxQaTxnTrash($_POST['content_id'],$_POST['sort_id'],$_POST['sort_dir'],$_POST['show_acct'],FALSE);
$widget->buildTrashWidget();
?>