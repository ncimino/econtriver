<?php
class QA_Account_Build {
	const C_AXN = 'QA_Account_AJAX';
	const C_ACCTS = 'accts';
	const C_SHARED = 'shared_accts';
	const C_OWNED = 'owned_accts';
	const C_DELETED = 'deleted_accts';
	
	const A_GET = 'get_acct';
	const A_CREATE = 'add_acct';
	const A_EDIT = 'edit_acct';
	const A_DELETE = 'delete_acct';
	const A_RESTORE = 'restore_acct';
	
	const I_CREATE = 'add_acct_text';
	const I_EDIT = 'account_text';
	
	const N_CREATE = 'new_acct_name';
	const N_NAME = 'acct_name';
	const N_ACCT_ID = 'acct';
	
	static function ownedTable($parentElement,$ownedAccounts,$parentId,$db) {
		if ($db->num($ownedAccounts)>0) {
			$divOwnedAccounts = new HTML_Div($parentElement,'',self::C_OWNED);
			self::table($divOwnedAccounts,$ownedAccounts,$parentId,'Owned Accounts:',self::C_OWNED.' acct_edit',$db);
		}
	}

	static function sharedTable($parentElement,$sharedAccounts,$parentId,$db) {
		if ($db->num($sharedAccounts)>0) {
			$divSharedAccounts = new HTML_Div($parentElement,'',self::C_SHARED);
			self::table($divSharedAccounts,$sharedAccounts,$parentId,'Shared Accounts:',self::C_SHARED,$db,false);
		}
	}

	static function deletedTable($parentElement,$deletedAccounts,$parentId,$db) {
		if ($db->num($deletedAccounts)>0) {
			$divOwnedAccounts = new HTML_Div($parentElement,'',self::C_DELETED);
			self::table($divOwnedAccounts,$deletedAccounts,$parentId,'Deleted Accounts:',self::C_DELETED,$db,false,true);
		}
	}

	static function table($parentElement,$queryResult,$parentId,$title,$tableName,$db,$editable=true,$restorable=false) {
		new HTML_Heading($parentElement,5,$title);
		$cols = ($restorable) ? 2 : 1;
		$cols = ($editable) ? 3 : $cols;
		$tableListAccounts = new Table($parentElement,$db->num($queryResult),$cols,$tableName);
		$i = 0;
		while ($account = $db->fetch($queryResult)) {
			$inputAccountName = new HTML_InputText($tableListAccounts->cells[$i][0],self::N_NAME,$account['name'],'',self::C_ACCTS);
			$inputAccountId = new HTML_InputHidden($tableListAccounts->cells[$i][0],self::N_ACCT_ID,$account['id']);
			if ($editable) {
				$editAxn = new Axn($tableListAccounts->cells[$i][1],'Edit',self::A_EDIT,$i,self::C_AXN);
				$deleteAxn = new Axn($tableListAccounts->cells[$i][2],'Delete',self::A_DELETE,$i,self::C_AXN);
				$deleteAxn->verifyDelete($account['name']);
				$editAxn->uses(array($inputAccountName,$inputAccountId));
				$deleteAxn->uses(array($inputAccountId));
			} elseif ($restorable) {
				$restoreAxn = new Axn($tableListAccounts->cells[$i][1],'Restore',self::A_RESTORE,$i,self::C_AXN);
				$restoreAxn->uses(array($inputAccountId));
				$inputAccountName->setAttribute('disabled',"disabled");
			} else {
				$inputAccountName->setAttribute('disabled',"disabled");
			}
			$i++;
		}
	}

	static function newForm($parentElement,$acctName,$parentId) {
		$divAddAccount = new HTML_Div($parentElement);
		new HTML_Heading($divAddAccount,5,'Add Account:');
		$inputAccountName = new HTML_InputText($divAddAccount,self::N_CREATE,$acctName,self::I_CREATE,self::C_ACCTS);
		$createAxn = new Axn($divAddAccount, 'Add Account', self::A_CREATE, '', self::C_AXN);
		$createAxn->uses(array($inputAccountName));		
	}
}
?>