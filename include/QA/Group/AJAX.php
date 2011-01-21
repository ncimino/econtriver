<?php
require_once '../../autoload.php';

try {
	$module = new QA_Group_Module($_POST['content_id']);
	
	if (isset($_POST[QA_Group_Build::C_AXN])) switch ($_POST[QA_Group_Build::C_AXN]) {
		case QA_Group_Build::A_CREATE:
			$module->addEntries($_POST[QA_Group_Build::N_CREATE]);
			break;
		case QA_Group_Build::A_DELETE:
			$module->dropEntries($_POST[QA_Group_Build::N_GRP_ID]);
			break;
		case QA_Group_Build::A_PERM_DELETE:
			$module->permDropEntries($_POST[QA_Group_Build::N_GRP_ID]);
			break;
		case QA_Group_Build::A_EDIT:
			$module->updateEntries($_POST[QA_Group_Build::N_NAME],$_POST[QA_Group_Build::N_GRP_ID]);
			break;
		case QA_Group_Build::A_GET:
			break;
		case QA_Group_Build::A_RESTORE:
			$module->restoreEntries($_POST[QA_Group_Build::N_GRP_ID]);
			break;
		default:
			$module->infoMsg->addMessage(-1,'A Management action was attempted, but was not found.');
			break;	
	} 
	
	$module->createModule();
} catch (Exception $e) { new ExceptionHandler($e); }

?>