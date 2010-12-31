<?php
require_once '../autoload.php';
if($_GET['var'] == 'getQaMsgsId') {
	echo QA_Widget::I_MSGS;
}
?>