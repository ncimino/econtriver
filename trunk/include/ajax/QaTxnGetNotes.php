<?php
require_once '../autoload.php';
$widget = new AjaxQaTxnNotes($_POST['content_id'],NULL,NULL,NULL,FALSE);
$widget->buildNotesWidget($_POST['txn_parent_id']);
?>