<?php
require_once '../../autoload.php';
$widget = new QA_Account_Widget($_POST['content_id']);
/*

if (isset($_POST[QA_Account_Build::C_AXN])) switch ($_POST[QA_Account_Build::C_AXN]) {
	case QA_Account_Build::A_CREATE:
		$widget->addEntries($_POST[QA_Account_Build::N_CREATE]);
		break;
	case QA_Account_Build::A_DELETE:
		$widget->dropEntries($_POST[QA_Account_Build::N_ACCT_ID]);
		break;
	case QA_Account_Build::A_EDIT:
		$widget->updateEntries($_POST[QA_Account_Build::N_NAME],$_POST[QA_Account_Build::N_ACCT_ID]);
		break;
	case QA_Account_Build::A_RESTORE:
		$widget->restoreEntries($_POST[QA_Account_Build::N_ACCT_ID]);
		break;
	case QA_Account_Build::A_GET:
		break;
	default:
		$widget->infoMsg->addMessage(-1,'An Account Management action was attempted, but was not found.');
		break;	
} 

$widget->createWidget();*/
?>