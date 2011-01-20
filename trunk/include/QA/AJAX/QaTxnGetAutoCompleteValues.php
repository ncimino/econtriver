<?php
require_once '../autoload.php';
$module = new QA_TxnAutoComplete();
$module->setFieldId($_GET['field_id']);
echo $module->returnAutoCompleteValues();
?>