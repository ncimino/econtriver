<?php
require_once '../autoload.php';
if($_GET['var'] == 'getQaMsgsId') {
	echo AjaxQaWidget::getQaMsgsId();
}
?>