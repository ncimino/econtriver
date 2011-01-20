<?php
require_once '../../autoload.php';
$module = new QA_Account_Module($_POST['content_id']);

if (isset($_POST[QA_Account_Build::C_AXN])) switch ($_POST[QA_Account_Build::C_AXN]) {
	case QA_Account_Build::A_CREATE:
		$module->addEntries($_POST[QA_Account_Build::N_CREATE]);
		break;
	case QA_Account_Build::A_DELETE:
		$module->dropEntries($_POST[QA_Account_Build::N_ACCT_ID]);
		break;
	case QA_Account_Build::A_EDIT:
		$module->updateEntries($_POST[QA_Account_Build::N_NAME],$_POST[QA_Account_Build::N_ACCT_ID]);
		break;
	case QA_Account_Build::A_RESTORE:
		$module->restoreEntries($_POST[QA_Account_Build::N_ACCT_ID]);
		break;
	case QA_Account_Build::A_GET:
		break;
	default:
		$module->infoMsg->addMessage(-1,'An Account Management action was attempted, but was not found.');
		break;	
} 

$module->createWidget();
?>