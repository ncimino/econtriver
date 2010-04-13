<?php
class AjaxQaTxns extends AjaxQaWidget {

	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		}
	}

	function buildWidget() {
		$divHead = new HTMLDiv($this->container,'txn_head_id','txn_head');
		new HTMLParagraph($divHead,'Accounts | Records 1-10/200 | Show All | Select Columns');
		
		$divNew = new HTMLDiv($this->container,'txn_new_id','txn_new');
		$tableNewTxn = new Table($divNew,1,6,'txn_new_table','txn_new');
		new HTMLText($tableNewTxn->cells[0][0],'Date Entered');
		new HTMLText($tableNewTxn->cells[0][1],'Transaction Date');
		new HTMLText($tableNewTxn->cells[0][2],'Description');
		new HTMLText($tableNewTxn->cells[0][3],'Credit');
		new HTMLText($tableNewTxn->cells[0][4],'Debit');
		new HTMLText($tableNewTxn->cells[0][5],'Attachment');
		
		$this->printHTML();
	}
	
}
?>