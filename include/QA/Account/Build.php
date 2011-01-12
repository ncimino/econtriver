<?php
class QA_Account_Build {
	const A_CREATE = 'add_acct';
	const A_DELETE = 'delete_acct';
	const A_EDIT = 'edit_acct';
	const A_GET = 'get_acct';
	const A_RESTORE = 'restore_acct';
	
	const C_AXN = 'QA_Account_AJAX';
	const C_ACCTS = 'accts';
	
	const I_CREATE = 'add_acct_text';
	const I_EDIT = 'account_text';
	
	const N_ACCT_ID = 'acct';
	const N_CREATE = 'new_acct_name';
	const N_NAME = 'acct_name';
	
	static function ownedTable($parentElement,$ownedAccounts,$parentId,$db) {
		if ($db->num($ownedAccounts)>0) {
			$divOwnedAccounts = new HTML_Div($parentElement,'',QA_Module::C_FRAME);
			self::table($divOwnedAccounts,$ownedAccounts,$parentId,'Owned Accounts:','acct_edit',$db);
		}
	}

	static function sharedTable($parentElement,$sharedAccounts,$parentId,$db) {
		if ($db->num($sharedAccounts)>0) {
			$divSharedAccounts = new HTML_Div($parentElement,'',QA_Module::C_FRAME);
			self::table($divSharedAccounts,$sharedAccounts,$parentId,'Shared Accounts:','',$db,false);
		}
	}

	static function deletedTable($parentElement,$deletedAccounts,$parentId,$db) {
		if ($db->num($deletedAccounts)>0) {
			$divOwnedAccounts = new HTML_Div($parentElement,'',QA_Module::C_FRAME);
			self::table($divOwnedAccounts,$deletedAccounts,$parentId,'Deleted Accounts:','',$db,false,true);
		}
	}

	static function table($parentElement,$queryResult,$parentId,$title,$tableName,$db,$editable=true,$restorable=false) {
		new HTML_Heading($parentElement,5,$title);
		$cols = ($restorable) ? 2 : 1;
		$cols = ($editable) ? 3 : $cols;
		$table = new Table($parentElement,$db->num($queryResult),$cols,$tableName);
		$i = 0;
		while ($account = $db->fetch($queryResult)) {
			$inputName = new HTML_InputText($table->cells[$i][0],self::N_NAME,$account['name'],'',self::C_ACCTS);
			$inputId = new HTML_InputHidden($table->cells[$i][0],self::N_ACCT_ID,$account['id']);
			if ($editable) {
				$editAxn = new Axn($table->cells[$i][1],'Edit',self::A_EDIT,$i,self::C_AXN);
				$deleteAxn = new Axn($table->cells[$i][2],'Delete',self::A_DELETE,$i,self::C_AXN);
				$deleteAxn->verifyDelete($account['name']);
				$editAxn->uses(array($inputName,$inputId));
				$deleteAxn->uses(array($inputId));
			} elseif ($restorable) {
				$restoreAxn = new Axn($table->cells[$i][1],'Restore',self::A_RESTORE,$i,self::C_AXN);
				$restoreAxn->uses(array($inputId));
				$inputName->setAttribute('disabled',"disabled");
			} else {
				$inputName->setAttribute('disabled',"disabled");
			}
			$i++;
		}
	}

	static function newForm($parentElement,$name,$parentId) {
		$divAddAccount = new HTML_Div($parentElement,'', QA_Module::C_FRAME);
		new HTML_Heading($divAddAccount,5,'Add Account:');
		$inputAccountName = new HTML_InputText($divAddAccount,self::N_CREATE,$name,self::I_CREATE,self::C_ACCTS);
		$createAxn = new Axn($divAddAccount, 'Add Account', self::A_CREATE, '', self::C_AXN);
		$createAxn->uses(array($inputAccountName));		
	}
}
?>