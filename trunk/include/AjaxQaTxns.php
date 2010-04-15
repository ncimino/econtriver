<?php
class AjaxQaTxns extends AjaxQaWidget {
	private $activeAccounts; // MySQL result

	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
	}
	
	function getActiveAccounts () {
		$sql = "SELECT q_acct.* 
				FROM q_acct,q_owners,q_share,q_user_groups
				WHERE ( q_acct.id=q_owners.acct_id
				  AND q_owners.owner_id={$this->user->getUserId()}
				  AND q_acct.active=1 ) 
				  OR ( q_acct.id=q_share.acct_id
				  AND q_share.group_id=q_user_groups.group_id
				  AND q_user_groups.user_id={$this->user->getUserId()}
				  AND q_user_groups.active=1
				  AND q_acct.active=1 )
				GROUP BY q_acct.id
				ORDER BY q_acct.name ASC;";
		$this->activeOwnedAccounts = $this->DB->query($sql);
	}

	function buildWidget() {
		$this->getActiveAccounts();
		$this->buildActions();
		$this->buildTxns();		
		$this->printHTML();
	}
	
	function buildActions() {
		$divActions = new HTMLDiv($this->container,'txn_actions_id','txn_actions');
		$selectAcct = new HTMLSelect($divActions,'show_acct','show_acct_id');
		new HTMLOption($selectAcct,'All Accounts','0');
		while($result = $this->DB->fetch($this->activeAccounts)) {
			new HTMLOption($selectAcct,$result['name'],$result['id']);
		}
	}
	
	function buildTxns() {
		$divNew = new HTMLDiv($this->container,'txn_new_id','txn_new');
		$tableNewTxn = new Table($divNew,1,6,'txn_new_table','txn_new');
		new HTMLText($tableNewTxn->cells[0][0],'Date Entered');
		new HTMLText($tableNewTxn->cells[0][1],'Transaction Date');
		new HTMLText($tableNewTxn->cells[0][2],'Description');
		new HTMLText($tableNewTxn->cells[0][3],'Credit');
		new HTMLText($tableNewTxn->cells[0][4],'Debit');
		new HTMLText($tableNewTxn->cells[0][5],'Attachment');
	}
	
}
?>