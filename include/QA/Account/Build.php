<?php
class QA_Account_Build {
	const T_OWNED = 'Owned Accounts:';
	const T_SHARED = 'Shared Accounts:';
	const T_DELETED = 'Deleted Accounts:';
	
	const C_CREATE = 'acct_axn';
	const C_EDIT = 'acct_axn';
	const C_SHARED = 'acct_axn';
	const C_OWNED = 'acct_axn';
	const C_DELETED = 'acct_axn';
	/*
	const C_CREATE = 'add_acct';
	const C_EDIT = 'account';
	const C_SHARED = 'shared_accts';
	const C_OWNED = 'owned_accts';
	const C_DELETED = 'deleted_accts';//*/
	
	const I_CREATE = 'add_acct_text';
	const I_EDIT = 'account_text';
	
	const N_CREATE = 'add_acct_name';
	const N_EDIT = 'account_name';
	
	static function buildOwnedAccountsTable($parentElement,$ownedAccounts,$parentId,$db) {
		if ($db->num($ownedAccounts)>0) {
			$divOwnedAccounts = new HTML_Div($parentElement,'',self::C_OWNED);
			self::buildAccountsTable($divOwnedAccounts,$ownedAccounts,$parentId,self::T_OWNED,self::C_OWNED,$db);
		}
	}

	static function buildSharedAccountsTable($parentElement,$sharedAccounts,$parentId,$db) {
		if ($db->num($sharedAccounts)>0) {
			$divSharedAccounts = new HTML_Div($parentElement,'',self::C_SHARED);
			self::buildAccountsTable($divSharedAccounts,$sharedAccounts,$parentId,self::T_SHARED,self::C_SHARED,$db,false);
		}
	}

	static function buildDeletedAccountsTable($parentElement,$deletedAccounts,$parentId,$db) {
		if ($db->num($deletedAccounts)>0) {
			$divOwnedAccounts = new HTML_Div($parentElement,'',self::C_DELETED);
			self::buildAccountsTable($divOwnedAccounts,$deletedAccounts,$parentId,self::T_DELETED,self::C_DELETED,$db,false,true);
		}
	}

	static function buildAccountsTable($parentElement,$queryResult,$parentId,$title,$tableName,$db,$editable=true,$restorable=false) {
		new HTML_Heading($parentElement,5,$title);
		$cols = ($restorable) ? 2 : 1;
		$cols = ($editable) ? 3 : $cols;
		$tableListAccounts = new Table($parentElement,$db->num($queryResult),$cols,$tableName);
		$i = 0;
		while ($account = $db->fetch($queryResult)) {
			$accountName = (empty($account['name'])) ? QA_Account_Select::nameById($this->getEditAcctId(),$this) : $account['name'];
			$inputId = QA_Account_Widget::I_FS.'_'.$account['id'];
			$inputName = self::N_EDIT.'_'.$account['id'];

			$inputEditAccount = new HTML_InputText($tableListAccounts->cells[$i][0],$inputName,$accountName,$inputId,self::C_EDIT);
			if ($editable) {
				$jsEdit = "QA_Account_AJAX_Edit('{$parentId}','{$inputId}','{$account['id']}');";
				$jsDrop = "if(confirmSubmit('Are you sure you want to delete the \'".$account['name']."\' account?')) { QA_Account_AJAX_Drop('{$parentId}','{$account['id']}'); }";
				$aEditAccount = new HTML_Anchor($tableListAccounts->cells[$i][1],'#','Edit');
				$aEditAccount->setAttribute('onclick',$jsEdit);
				$aDropAccount = new HTML_Anchor($tableListAccounts->cells[$i][2],'#','Delete');
				$aDropAccount->setAttribute('onclick',$jsDrop);
			} elseif ($restorable) {
				$jsRestore = "QA_Account_AJAX_Restore('{$parentId}','{$account['id']}');";
				$inputEditAccount->setAttribute('disabled',"disabled");
				$aRestoreAccount = new HTML_Anchor($tableListAccounts->cells[$i][1],'#','Restore');
				$aRestoreAccount->setAttribute('onclick',$jsRestore);
			} else {
				$inputEditAccount->setAttribute('disabled',"disabled");
			}
			$i++;
		}
	}

	static function buildCreateAccountForm($parentElement,$acctName,$parentId) {
		$divAddAccount = new HTML_Div($parentElement,'',self::C_CREATE);
		new HTML_Heading($divAddAccount,5,'Add Account:');
		$inputAddAccount = new HTML_InputText($divAddAccount,self::N_CREATE,$acctName,self::I_CREATE,self::C_CREATE);
		$inputAddAccount->setAttribute('onkeypress',"enterCall(event,function() {QA_Account_AJAX_Add('{$parentId}','".self::I_CREATE."');})");
		$aAddAccount = new HTML_Anchor($divAddAccount,'#','Add Account');
		$aAddAccount->setAttribute('onclick',"QA_Account_AJAX_Add('{$parentId}','".self::I_CREATE."');");
	}
}
?>