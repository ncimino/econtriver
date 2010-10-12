<?php
class AjaxQaTxnTrash extends AjaxQaTxns {
	private $inactiveTxns; // MySQL result
	private $parentTxns; // MySQL result
	private $numberOfColumns = 11;
	private $titleRowHeight = 1;

	function __construct($parentId,$sortId=NULL,$sortDir=NULL,$showAcct=NULL,$showMsgDiv=TRUE) {
		parent::__construct($parentId,$sortId=NULL,$sortDir=NULL,$showAcct=NULL,$showMsgDiv=TRUE);
	}

	function buildTrashWidget() {
		$this->activeAccounts = AjaxQaSelectAccounts::getActiveAccounts($this->user->getUserId(),$this->DB);
		$this->getTxnTrash();
		new HTMLHeading($this->container,3,'Trash Bin for: '.AjaxQaSelectAccounts::getAccountNameById($this->showAcct,$this->DB,TRUE));
		if (($this->inactiveTxns) and ($this->DB->num($this->inactiveTxns) != 0)) {
			$rows = $this->DB->num($this->inactiveTxns);
			$tableTxn = new Table($this->container,$rows+$this->titleRowHeight,$this->numberOfColumns,'txnt_table','txnt');
			$this->buildTxnTrashTitles($tableTxn,$row = 0);
			$this->buildTxnTrash($tableTxn,++$row);
		} else {
			$divTrashBin = new HTMLDiv($this->container,'trash_bin','');
			new HTMLText($divTrashBin,'There are no items in your trash bin, for the selected account(s). Try changing the account you are viewing.');
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
				FROM q_txn,user,q_acct
				WHERE {$parent['id']} = q_txn.parent_txn_id
				  AND q_txn.user_id = user.user_id
				  AND q_txn.acct_id = q_acct.id
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
				FROM q_txn, q_acct 
				WHERE (".AjaxQaSelectAccounts::getSqlAcctsToShow($this->showAcct,$this->activeAccounts,$this->user->getUserId(),$this->DB).")
				AND q_txn.active = 0
				AND q_txn.acct_id = q_acct.id 
				AND q_txn.parent_txn_id = q_txn.id;";
		return $this->parentTxns = $this->DB->query($sql);
	}

	function buildTxnTrashTitles($tableTxn,$row = 0) {
		$col = 0;

		new HTMLText($tableTxn->cells[$row][$col++],'Account');
		new HTMLText($tableTxn->cells[$row][$col++],'User');
		new HTMLText($tableTxn->cells[$row][$col++],'Entered');
		new HTMLText($tableTxn->cells[$row][$col++],'Date');
		new HTMLText($tableTxn->cells[$row][$col++],'Type');
		new HTMLText($tableTxn->cells[$row][$col++],'Establishment');
		new HTMLText($tableTxn->cells[$row][$col++],'Note');
		new HTMLText($tableTxn->cells[$row][$col++],'Credit');
		new HTMLText($tableTxn->cells[$row][$col++],'Debit');
		new HTMLText($tableTxn->cells[$row][$col++],'Bank');
		new HTMLText($tableTxn->cells[$row][$col++],'Actions');
	}

	function buildTxnTrash($tableTxn,$row = 0) {
		if ($this->inactiveTxns) {
			while($txn = $this->DB->fetch($this->inactiveTxns)) {
				$col = 0;
				$oddOrEven = ($row % 2 == 0) ? "odd" : "even";
				$selectAcct = new HTMLSelect($tableTxn->cells[$row][$col++],'txnt_acct_'.$txn['id'],'txnt_acct_'.$txn['id'],'txnt_acct_select_'.$oddOrEven);
				$this->DB->resetRowPointer($this->activeAccounts);
				while($result = $this->DB->fetch($this->activeAccounts)) {
					$selected = ($txn['acct_id'] == $result['id']) ? TRUE : FALSE;
					new HTMLOption($selectAcct,$result['name'],$result['id'],$selected);
				}

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable');
				new HTMLText($tableTxn->cells[$row][$col++],$txn['handle']);

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable number');
				new HTMLText($tableTxn->cells[$row][$col++],date($this->user->getDateFormat(),$txn['entered']));

				new HTMLInputText($tableTxn->cells[$row][$col++],'txnt_date_'.$txn['id'],date($this->user->getDateFormat(),$txn['date']),'txnt_date_'.$txn['id'],'dateselection txn_input number');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txnt_type_'.$txn['id'],$txn['type'],'txnt_type_'.$txn['id'],'txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txnt_establishment_'.$txn['id'],$txn['establishment'],'txnt_establishment_'.$txn['id'],'txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txnt_note_'.$txn['id'],$txn['note'],'txnt_note_'.$txn['id'],'txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txnt_credit_'.$txn['id'],$txn['credit'],'txnt_credit_'.$txn['id'],'txn_input number credit');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txnt_debit_'.$txn['id'],$txn['debit'],'txnt_debit_'.$txn['id'],'txn_input number debit');

				$checked = ($txn['banksays']) ? TRUE : FALSE;
				$checkBox = new HTMLInputCheckbox($tableTxn->cells[$row][$col],'txnt_banksays_'.$txn['id'],'txnt_banksays_'.$txn['id'],'txnt_banksays_check',$checked);
				$checkBox->setAttribute('disabled','disabled');
				$parent_id = ($txn['parent_txn_id']) ? $txn['parent_txn_id'] : $txn['id'];
				new HTMLInputHidden($tableTxn->cells[$row][$col++],'txnt_parent_id_'.$txn['id'],$parent_id,'txnt_parent_id_'.$txn['id']);

				$makeActive = new HTMLAnchor($tableTxn->cells[$row][$col],'#','','txnt_make_active_anchor_'.$txn['id'],'txnt_make_active');
				$makeActive->setTitle('Make Active');
				new HTMLSpan($makeActive,'','txnt_make_active_'.$txn['id'],'ui-icon ui-icon-arrowreturnthick-1-n ui-float-left');
					
				$row++;
			}
		} else {
			$this->infoMsg->addMessage(-1,'There was a problem retrieving the trash data.');
		}
	}
}
?>