<?php
class QA_TxnHistory extends QA_Txns {
	private $txnHistory; // MySQL result

	function __construct($parentId,$sortId=NULL,$sortDir=NULL,$showAcct=NULL,$showMsgDiv=TRUE) {
		parent::__construct($parentId,$sortId=NULL,$sortDir=NULL,$showAcct=NULL,$showMsgDiv=TRUE);
	}

	function buildHistoryWidget($txn_id) {
		$this->activeAccounts = QA_Account_Selector::getActiveAccounts($this->user->getUserId(),$this->DB);
		$this->getTxnHistory($txn_id);
		$rows = $this->DB->num($this->txnHistory);
		new HTML_Heading($this->container,3,'History');
		$tableTxn = new Table($this->container,$rows + 1,11,'txnh_table_'.$txn_id,'txnh');
		$this->buildTxnHistoryTitles($tableTxn,$row = 0);
		$this->buildTxnHistory($tableTxn,++$row,$txn_id);
		$this->printHTML();
	}

	function getTxnHistory($txn_id) {
		$sql = "SELECT q_txn.parent_txn_id
				FROM q_txn
				WHERE q_txn.id = {$txn_id};";
		$parent = $this->DB->fetch($this->DB->query($sql));
		$sql = "SELECT q_txn.*,user.handle
				FROM q_txn,user
				WHERE ( q_txn.id={$parent['parent_txn_id']}
				   OR q_txn.parent_txn_id={$parent['parent_txn_id']} )
				  AND q_txn.user_id = user.user_id
				ORDER BY q_txn.entered DESC;";
		$this->txnHistory = $this->DB->query($sql);
	}

	function buildTxnHistoryTitles($tableTxn,$row = 0) {
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

	function buildTxnHistory($tableTxn,$row = 0,$active_txn_id) {
		if ($this->txnHistory) {
			while($txn = $this->DB->fetch($this->txnHistory)) {
				$col = 0;
				$oddOrEven = ($row % 2 == 0) ? "odd" : "even";
				$current_txn = ($row == 1) ? " current_txn" : "";
				$selectAcct = new HTML_Select($tableTxn->cells[$row][$col++],'txnh_acct_'.$txn['id'],'txnh_acct_'.$txn['id'],'txnh_acct_select_'.$oddOrEven.$current_txn);
				$this->DB->resetRowPointer($this->activeAccounts);
				while($result = $this->DB->fetch($this->activeAccounts)) {
					$selected = ($txn['acct_id'] == $result['id']) ? TRUE : FALSE;
					new HTML_Option($selectAcct,$result['name'],$result['id'],$selected);
				}

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable');
				new HTML_Text($tableTxn->cells[$row][$col++],$txn['handle']);

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable number');
				new HTML_Text($tableTxn->cells[$row][$col++],date($this->user->getDateFormat().' g:i:s a',$txn['entered']));

				new HTML_InputText($tableTxn->cells[$row][$col++],'txnh_date_'.$txn['id'],date($this->user->getDateFormat(),$txn['date']),'txnh_date_'.$txn['id'],'dateselection txn_input number');
				new HTML_InputText($tableTxn->cells[$row][$col++],'txnh_type_'.$txn['id'],$txn['type'],'txnh_type_'.$txn['id'],'txn_input');
				new HTML_InputText($tableTxn->cells[$row][$col++],'txnh_establishment_'.$txn['id'],$txn['establishment'],'txnh_establishment_'.$txn['id'],'txn_input');
				new HTML_InputText($tableTxn->cells[$row][$col++],'txnh_note_'.$txn['id'],$txn['note'],'txnh_note_'.$txn['id'],'txn_input');
				new HTML_InputText($tableTxn->cells[$row][$col++],'txnh_credit_'.$txn['id'],$txn['credit'],'txnh_credit_'.$txn['id'],'txn_input number credit');
				new HTML_InputText($tableTxn->cells[$row][$col++],'txnh_debit_'.$txn['id'],$txn['debit'],'txnh_debit_'.$txn['id'],'txn_input number debit');

				$checked = ($txn['banksays']) ? TRUE : FALSE;
				$checkBox = new HTML_InputCheckbox($tableTxn->cells[$row][$col],'txnh_banksays_'.$txn['id'],'txnh_banksays_'.$txn['id'],'txnh_banksays_check',$checked);
				$checkBox->setAttribute('disabled','disabled');
				$parent_id = ($txn['parent_txn_id']) ? $txn['parent_txn_id'] : $txn['id'];
				new HTML_InputHidden($tableTxn->cells[$row][$col++],'txnh_parent_id_'.$txn['id'],$parent_id,'txnh_parent_id_'.$txn['id']);

				if ($row == 1) {
					for($i=0;$i<=$col;$i++) {
						$tableTxn->cells[$row][$i]->setClass($tableTxn->cells[$row][$i]->getClass().$current_txn);
					}
				} else {
					$makeActive = new HTML_Anchor($tableTxn->cells[$row][$col],'#','','txnh_make_active_anchor_'.$txn['id'],'txnh_make_active');
					$makeActive->setTitle('Make Active');
					new HTML_Span($makeActive,'','txnh_make_active_'.$txn['id'],'ui-icon ui-icon-arrowreturnthick-1-n ui-float-left');
					new HTML_InputHidden($tableTxn->cells[$row][$col],'txnh_make_inactive_'.$txn['id'],$active_txn_id,'txnh_make_inactive_'.$txn['id']);
				}

				$row++;
			}
		} else {
			$this->infoMsg->addMessage(-1,'There was a problem retrieving the transaction data.');
		}
	}
}
?>