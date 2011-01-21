<?php
require_once '../autoload.php';
$module = new QA_Txns($_POST['content_id'],$_POST['sort_id'],$_POST['sort_dir'],$_POST['show_acct']);
$entryWasAdded = $module->addEntries($_POST['acct'],$_POST['date'],$_POST['type'],$_POST['establishment'],$_POST['note'],$_POST['credit'],$_POST['debit'],
					$_POST['parent_id'],$_POST['banksays'],$_POST['current_txn_id'],$_POST['active_txn_id']);
if($entryWasAdded) $module->dropEntries($_POST['active_txn_id'],$_POST['log'],$_POST['current_txn_id']);
$module->createModule();

?>