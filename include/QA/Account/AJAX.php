<?php
require_once '../../autoload.php';

$widget = new QA_Account_Widget($_POST['content_id']);

echo $_POST['action'];

switch ($_POST['action']) {
	case 'acct_add':
		$widget->addEntries($_POST['name']);
		break;
	case 'acct_drop':
		$widget->dropEntries($_POST['acct_id']);
		break;
	case 'acct_edit':
		$widget->updateEntries($_POST['name'],$_POST['acct_id']);
		break;
	case 'acct_restore':
		$widget->restoreEntries($_POST['acct_id']);
		break;
	default: // covers get
		break;	
} 

$widget->createWidget();
?>