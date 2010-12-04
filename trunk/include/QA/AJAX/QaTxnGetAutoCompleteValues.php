<?php
require_once '../autoload.php';
$widget = new AjaxQaTxnAutoComplete();
$widget->setFieldId($_GET['field_id']);
echo $widget->returnAutoCompleteValues();
?>