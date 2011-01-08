<?php
class QA_TxnTrash extends QA_Txns {
	private $inactiveTxns; // MySQL result
	private $parentTxns; // MySQL result
	private $numberOfColumns = 11;
	private $titleRowHeight = 1;

	function __construct($parentId,$sortId=NULL,$sortDir=NULL,$selectedAcct=NULL,$showMsgDiv=TRUE) {
		parent::__construct($parentId,$sortId=NULL,$sortDir=NULL,$selectedAcct=NULL,$showMsgDiv=TRUE);
	}

	function buildTrashWidget() {
		$this->activeAccounts = QA_Account_Select::byMember($this->user->getUserId(),$this->DB);
		$this->getTxnTrash();
		new HTML_Heading($this->container,3,'Trash Bin for: '.QA_Account_Select::nameById($this->selectedAcct,$this->DB,TRUE));
		if (($this->inactiveTxns) and ($this->DB->num($this->inactiveTxns) != 0)) {
			$rows = $this->DB->num($this->inactiveTxns);
			$tableTxn = new Table($this->container,$rows+$this->titleRowHeight,$this->numberOfColumns,'txnt_table','txnt');
			$this->buildTxnTrashTitles($tableTxn,$row = 0);
			$this->buildTxnTrash($tableTxn,++$row);
		} else {
			$divTrashBin = new HTML_Div($this->container,'trash_bin','');
			new HTML_Text($divTrashBin,'There are no items in your trash bin, for the selected account(s). Try changing the account you are viewing.');
		}
		$this->printHTML();
	}

	function getTxnTrash() {
		$this->getParentTxns();
		if ($this->DB->num($this->parentTxns) != 0) {
			$sql = "";
			while($parent = $this->DB->fetch($this->parentTxns)) {
				if ($sql != "") $sql .= " UNION ";
				$sql .= "SELECT q_txn.*,user.handle
				FROM q_txn,user,".QA_DB_Table::ACCT."
				WHERE {$parent['id']} = q_txn.parent_txn_id
				  AND q_txn.user_id = user.user_id
				  AND q_txn.acct_id = ".QA_DB_Table::ACCT.".id
				  AND q_txn.active = 0
				  AND q_txn.entered = (SELECT max(q_txn.entered) FROM q_txn WHERE {$parent['id']} = q_txn.parent_txn_id)";
			}
			$sql .= " ORDER BY entered DESC;";
			return $this->inactiveTxns = $this->DB->query($sql);
		} else {
			return $this->inactiveTxns = false;
		}
	}

	function getParentTxns() {
		$sql = "SELECT q_txn.*
				FROM q_txn, ".QA_DB_Table::ACCT." 
				WHERE (".QA_Account_Select::acctsToShow($this->selectedAcct,$this->activeAccounts,$this->user->getUserId(),$this->DB).")
				AND q_txn.active = 0
				AND q_txn.acct_id = ".QA_DB_Table::ACCT.".id 
				AND q_txn.parent_txn_id = q_txn.id;";
		return $this->parentTxns = $this->DB->query($sql);
	}

	function buildTxnTrashTitles($tableTxn,$row = 0) {
		$col = 0;

		new HTML_Text($tableTxn->cells[$row][$col++],'Account');
		new HTML_Text($tableTxn->cells[$row][$col++],'User');
		new HTML_Text($tableTxn->cells[$row][$col++],'Entered');
		new HTML_Text($tableTxn->cells[$row][$col++],'Date');
		new HTML_Text($tableTxn->cells[$row][$col++],'Type');
		new HTML_Text($tableTxn->cells[$row][$col++],'Establishment');
		new HTML_Text($tableTxn->cells[$row][$col++],'Note');
		new HTML_Text($tableTxn->cells[$row][$col++],'Credit');
		new HTML_Text($tableTxn->cells[$row][$col++],'Debit');
		new HTML_Text($tableTxn->cells[$row][$col++],'Bank');
		new HTML_Text($tableTxn->cells[$row][$col++],'Actions');
	}

	function buildTxnTrash($tableTxn,$row = 0) {
		if ($this->inactiveTxns) {
			while($txn = $this->DB->fetch($this->inactiveTxns)) {
				$col = 0;
				$oddOrEven = ($row % 2 == 0) ? "odd" : "even";
				$selectAcct = new HTML_Select($tableTxn->cells[$row][$col++],'txnt_acct_'.$txn['id'],'txnt_acct_'.$txn['id'],'txnt_acct_select_'.$oddOrEven);
				$this->DB->resetRowPointer($this->activeAccounts);
				while($result = $this->DB->fetch($this->activeAccounts)) {
					$selected = ($txn['acct_id'] == $result['id']) ? TRUE : FALSE;
					new HTML_Option($selectAcct,$result['name'],$result['id'],$selected);
				}

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable');
				new HTML_Text($tableTxn->cells[$row][$col++],$txn['handle']);

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable number');
				new HTML_Text($tableTxn->cells[$row][$col++],date($this->user->getDateFormat(),$txn['entered']));

				new HTML_InputText($tableTxn->cells[$row][$col++],'txnt_date_'.$txn['id'],date($this->user->getDateFormat(),$txn['date']),'txnt_date_'.$txn['id'],'dateselection txn_input number');
				new HTML_InputText($tableTxn->cells[$row][$col++],'txnt_type_'.$txn['id'],$txn['type'],'txnt_type_'.$txn['id'],'txn_input');
				new HTML_InputText($tableTxn->cells[$row][$col++],'txnt_establishment_'.$txn['id'],$txn['establishment'],'txnt_establishment_'.$txn['id'],'txn_input');
				new HTML_InputText($tableTxn->cells[$row][$col++],'txnt_note_'.$txn['id'],$txn['note'],'txnt_note_'.$txn['id'],'txn_input');
				new HTML_InputText($tableTxn->cells[$row][$col++],'txnt_credit_'.$txn['id'],$txn['credit'],'txnt_credit_'.$txn['id'],'txn_input number credit');
				new HTML_InputText($tableTxn->cells[$row][$col++],'txnt_debit_'.$txn['id'],$txn['debit'],'txnt_debit_'.$txn['id'],'txn_input number debit');

				$checked = ($txn['banksays']) ? TRUE : FALSE;
				$checkBox = new HTML_InputCheckbox($tableTxn->cells[$row][$col],'txnt_banksays_'.$txn['id'],'txnt_banksays_'.$txn['id'],'txnt_banksays_check',$checked);
				$checkBox->setAttribute('disabled','disabled');
				$parent_id = ($txn['parent_txn_id']) ? $txn['parent_txn_id'] : $txn['id'];
				new HTML_InputHidden($tableTxn->cells[$row][$col++],'txnt_parent_id_'.$txn['id'],$parent_id,'txnt_parent_id_'.$txn['id']);

				$makeActive = new HTML_Anchor($tableTxn->cells[$row][$col],'#','','txnt_make_active_anchor_'.$txn['id'],'txnt_make_active');
				$makeActive->setTitle('Make Active');
				new HTML_Span($makeActive,'','txnt_make_active_'.$txn['id'],'ui-icon ui-icon-arrowreturnthick-1-n ui-float-left');
					
				$row++;
			}
		} else {
			$this->infoMsg->addMessage(-1,'There was a problem retrieving the trash data.');
		}
	}
}
?>