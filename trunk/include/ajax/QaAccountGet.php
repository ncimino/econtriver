<?php
require_once '../autoload.php';
$widget = new AjaxQaAccounts($_POST['content_id']);
//for($i=0;$i<100000000;$i++ ) { }
$widget->buildWidget();
?>