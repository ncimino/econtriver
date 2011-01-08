<?php
require_once '../../autoload.php';

try {
	$widget = new QA_Group_Widget($_POST['content_id']);
	
	if (isset($_POST[QA_Group_Build::C_AXN])) switch ($_POST[QA_Group_Build::C_AXN]) {
		case QA_Group_Build::A_CREATE:
			$widget->addEntries($_POST[QA_Group_Build::N_CREATE]);
			break;
		case QA_Group_Build::A_DELETE:
			$widget->dropEntries($_POST[QA_Group_Build::N_GRP_ID]);
			break;
		case QA_Group_Build::A_PERM_DELETE:
			$widget->permDropEntries($_POST[QA_Group_Build::N_GRP_ID]);
			break;
		case QA_Group_Build::A_EDIT:
			$widget->updateEntries($_POST[QA_Group_Build::N_NAME],$_POST[QA_Group_Build::N_GRP_ID]);
			break;
		case QA_Group_Build::A_GET:
			break;
		case QA_Group_Build::A_RESTORE:
			$widget->restoreEntries($_POST[QA_Group_Build::N_GRP_ID]);
			break;
		default:
			$widget->infoMsg->addMessage(-1,'A Management action was attempted, but was not found.');
			break;	
	} 
	
	$widget->createWidget();
} catch (Exception $e) { new ExceptionHandler($e); }

?>