<?php
class QA_Account_Builder {
	static function buildOwnedAccountsTable($parentElement) {
		if ($this->DB->num($this->ownedAccounts)>0) {
			$divOwnedAccounts = new HTML_Div($parentElement,'',self::getOwnedAcctClass());
			$this->buildAccountsTable($divOwnedAccounts,'Owned Accounts:',$this->ownedAccounts,self::getOwnedAcctClass());
		}
	}

	static function buildSharedAccountsTable($parentElement) {
		if ($this->DB->num($this->sharedAccounts)>0) {
			$divSharedAccounts = new HTML_Div($parentElement,'',self::getSharedAcctClass());
			$this->buildAccountsTable($divSharedAccounts,'Shared Accounts:',$this->sharedAccounts,self::getSharedAcctClass(),false);
		}
	}

	static function buildDeletedAccountsTable($parentElement) {
		if ($this->DB->num($this->deletedAccounts)>0) {
			$divOwnedAccounts = new HTML_Div($parentElement,'',self::getDeletedAcctClass());
			$this->buildAccountsTable($divOwnedAccounts,'Deleted Accounts:',$this->deletedAccounts,self::getDeletedAcctClass(),false,true);
		}
	}

	static function buildAccountsTable($parentElement,$title,$queryResult,$tableName,$editable=true,$restorable=false) {
		new HTML_Heading($parentElement,5,$title);
		$cols = ($restorable) ? 2 : 1;
		$cols = ($editable) ? 3 : $cols;
		$tableListAccounts = new Table($parentElement,$this->DB->num($queryResult),$cols,$tableName);
		$i = 0;
		while ($account = $this->DB->fetch($queryResult)) {
			$accountName = (empty($account['name'])) ? QA_Account_Selector::getAccountNameById($this->getEditAcctId(),$this) : $account['name'];
			$inputId = $this->getEditAcctNameInId().'_'.$account['id'];
			$inputName = $this->getEditAcctNameInName().'_'.$account['id'];

			$inputEditAccount = new HTML_InputText($tableListAccounts->cells[$i][0],$inputName,$accountName,$inputId,self::editAcctNameClass);
			if ($editable) {
				$jsEdit = "QA_Account_AJAX_Edit('{$this->parentId}','{$inputId}','{$account['id']}');";
				$jsDrop = "if(confirmSubmit('Are you sure you want to delete the \'".$account['name']."\' account?')) { QA_Account_AJAX_Drop('{$this->parentId}','{$account['id']}'); }";
				$aEditAccount = new HTML_Anchor($tableListAccounts->cells[$i][1],'#','Edit');
				$aEditAccount->setAttribute('onclick',$jsEdit);
				$aDropAccount = new HTML_Anchor($tableListAccounts->cells[$i][2],'#','Delete');
				$aDropAccount->setAttribute('onclick',$jsDrop);
			} elseif ($restorable) {
				$jsRestore = "QA_Account_AJAX_Restore('{$this->parentId}','{$account['id']}');";
				$inputEditAccount->setAttribute('disabled',"disabled");
				$aRestoreAccount = new HTML_Anchor($tableListAccounts->cells[$i][1],'#','Restore');
				$aRestoreAccount->setAttribute('onclick',$jsRestore);
			} else {
				$inputEditAccount->setAttribute('disabled',"disabled");
			}
			$i++;
		}
	}

	static function buildCreateAccountForm($parentElement) {
		$divAddAccount = new HTML_Div($parentElement,'',$this->getCreateAcctClass());
		new HTML_Heading($divAddAccount,5,'Add Account:');
		$inputAddAccount = new HTML_InputText($divAddAccount,$this->getCreateAcctInName(),$this->acctName,$this->getCreateAcctInId(),$this->getCreateAcctClass());
		$inputAddAccount->setAttribute('onkeypress',"enterCall(event,function() {QA_Account_AJAX_Add('{$this->parentId}','{$this->getCreateAcctInId()}');})");
		$aAddAccount = new HTML_Anchor($divAddAccount,'#','Add Account');
		$aAddAccount->setAttribute('onclick',"QA_Account_AJAX_Add('{$this->parentId}','{$this->getCreateAcctInId()}');");
	}
}
?>