<?php
require_once '../autoload.php';
$widget = new QA_TxnAutoComplete();
$widget->setFieldId($_GET['field_id']);
echo $widget->returnAutoCompleteValues();
?>