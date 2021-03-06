<?php
class QA_TxnAutoComplete extends QA_Txns {
	private $txnAutoCompleteValues;
	private $txnAutoCompleteValuesSerialized;
	private $txnAutoCompleteFieldId;
	
	function __construct() {
		parent::__construct($sortId=NULL,$sortDir=NULL,$selectedAcct=NULL,$showMsgDiv=FALSE);
		$this->activeAccounts = QA_Account_Select::byMember($this->user->getUserId(),$this->DB);
	}
	
	function setFieldId($id) {
		$this->txnAutoCompleteFieldId = $id; 
	}

	function pullAutoCompleteValues() {
		$sql = "SELECT DISTINCT q_txn.{$this->txnAutoCompleteFieldId} FROM q_txn,user,".QA_DB_Table::ACCT."
					WHERE (".QA_Account_Select::byMember($this->activeAccounts,$this->DB).")
					  AND q_txn.active = 1
					  AND q_txn.user_id = user.user_id
					  AND q_txn.acct_id = ".QA_DB_Table::ACCT.".id
					GROUP BY q_txn.id
					ORDER BY q_txn.{$this->txnAutoCompleteFieldId} ASC;";
		$this->txnAutoCompleteValues = $this->DB->query($sql);
	}
	
	function returnAutoCompleteValues() {
		$this->pullAutoCompleteValues();
		$this->serializeAutoCompleteValues();
		return $this->txnAutoCompleteValuesSerialized;
	}
	
	function serializeAutoCompleteValues() {
		$this->txnAutoCompleteValuesSerialized = "";
		$this->DB->resetRowPointer($this->txnAutoCompleteValues);
		$i = 0;
		while ($currentValue = $this->DB->fetch($this->txnAutoCompleteValues)) {
			if ($i++ > 0) $this->txnAutoCompleteValuesSerialized .= "--QaAjaxDelimeter--";
			$this->txnAutoCompleteValuesSerialized .= $currentValue[$this->txnAutoCompleteFieldId];
		}
	}
}
?>