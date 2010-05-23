<?php
class AjaxQaTxns extends AjaxQaWidget {
	private $activeAccounts; // MySQL result
	private $txnHistory; // MySQL result
	private $activeTxns; // MySQL result
	private $activeTxnsSum = 0; // MySQL result
	private $activeTxnsBankSaysSum = 0; // MySQL result
	private $newTxnValues = array();
	private $sortDir = 'DESC';
	private $sortField = 'q_txn.date';
	private $showAcct = 0;

	function __construct($parentId,$sortId=NULL,$sortDir=NULL,$showAcct=NULL,$showMsgDiv=TRUE) {
		parent::__construct($showMsgDiv);
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		} else {
			if (isset($sortDir)) $this->sortDir = $sortDir;
			if (isset($sortId)) $this->sortField = $this->convIdToField($sortId);
			if (isset($showAcct)) $this->setShowAcct($showAcct);
			else $this->getShowAcct();
			$this->newTxnValues['acct'] = 0;
			$this->newTxnValues['date'] = date($this->user->getDateFormat(),$this->user->getTime());
			$this->newTxnValues['type'] = '';
			$this->newTxnValues['establishment'] = '';
			$this->newTxnValues['note'] = '';
			$this->newTxnValues['credit'] = '';
			$this->newTxnValues['debit'] = '';
		}
	}

	function setShowAcct($acct_id) {
		if (QaSettings::setSetting('show_acct',$this->user->getUserId(),$acct_id,$this->DB)) {
			$this->showAcct = $acct_id;
		}
	}

	function getShowAcct() {
		if ($acct_id = QaSettings::getSetting('show_acct',$this->user->getUserId(),$this->DB)) {
			$this->showAcct = $acct_id;
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
		$this->activeAccounts = $this->DB->query($sql);
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

	function buildWidget() {
		$this->getActiveAccounts();
		if ($this->DB->num($this->activeAccounts)) {
			$this->buildActions();
			$this->buildTxnsTable();
		} else {
			new HTMLText($this->container,'There are no active accounts. You must have an active account before you can add transactions.');
		}
		$this->printHTML();
	}

	function buildActions() {
		$divActions = new HTMLDiv($this->container,'txn_actions_id','txn_actions');
		$selectAcct = new HTMLSelect($divActions,'show_acct','show_acct');
		new HTMLOption($selectAcct,'All Accounts',0);
		while($result = $this->DB->fetch($this->activeAccounts)) {
			$selected = ($result['id'] == $this->showAcct) ? TRUE : FALSE;
			new HTMLOption($selectAcct,$result['name'],$result['id'],$selected);
		}
	}

	function buildTxnsTable() {
		$divNew = new HTMLDiv($this->container,'txn_id','txn');
		$this->getTxns();
		$this->getTxnsSum();
		$this->getTxnsBankSaysSum();
		$rows = $this->DB->num($this->activeTxns) * 3 + 2;
		$tableTxn = new Table($divNew,$rows,12,'txn_table','txn');
		$this->buildTxnTitles($tableTxn,0);
		$this->buildNewTxns($tableTxn,1);
		$this->buildTxns($tableTxn,2);
	}

	function convIdToField($input) {
		switch($input) {
			case 'q_acct.name': 				$return = 'txn_title_acctname';	break;
			case 'txn_title_acctname' :			$return = 'q_acct.name'; break;
			case 'user.handle':					$return = 'txn_title_handle'; break;
			case 'txn_title_handle' :			$return = 'user.handle'; break;
			case 'q_txn.entered':				$return = 'txn_title_entered'; break;
			case 'txn_title_entered' :			$return = 'q_txn.entered'; break;
			case 'q_txn.date':					$return = 'txn_title_date'; break;
			case 'txn_title_date' :				$return = 'q_txn.date'; break;
			case 'q_txn.type':					$return = 'txn_title_type'; break;
			case 'txn_title_type' :				$return = 'q_txn.type'; break;
			case 'q_txn.establishment':			$return = 'txn_title_establishment'; break;
			case 'txn_title_establishment' :	$return = 'q_txn.establishment'; break;
			case 'q_txn.note':					$return = 'txn_title_note'; break;
			case 'txn_title_note' :				$return = 'q_txn.note'; break;
			case 'q_txn.credit':				$return = 'txn_title_credit'; break;
			case 'txn_title_credit' :			$return = 'q_txn.credit'; break;
			case 'q_txn.debit':					$return = 'txn_title_debit'; break;
			case 'txn_title_debit' :			$return = 'q_txn.debit'; break;
		}
		return $return;
	}

	function buildTxnTitles($tableTxn,$row) {
		$col = 0;

		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Account','q_acct.name',$this->convIdToField('q_acct.name'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'User','user.handle',$this->convIdToField('user.handle'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Date Entered','q_txn.entered',$this->convIdToField('q_txn.entered'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Transaction Date','q_txn.date',$this->convIdToField('q_txn.date'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Type','q_txn.type',$this->convIdToField('q_txn.type'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Establishment','q_txn.establishment',$this->convIdToField('q_txn.establishment'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Note','q_txn.note',$this->convIdToField('q_txn.note'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Credit','q_txn.credit',$this->convIdToField('q_txn.credit'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Debit','q_txn.debit',$this->convIdToField('q_txn.debit'));
		new HTMLText($tableTxn->cells[$row][$col++],'Balance');
		new HTMLText($tableTxn->cells[$row][$col++],'Bank Says');
		new HTMLText($tableTxn->cells[$row][$col++],'Actions');
	}

	function addSortableTitle($parentElement,$title,$fieldName,$id) {
		if ($this->sortField == $fieldName) {
			$nextSortDir = ($this->sortDir == 'DESC') ? 'ASC' : 'DESC';
			$link = new HTMLAnchor($parentElement,"#",'',$id,'txn_title');
			$curArrow = ($this->sortDir == 'DESC') ? 'ui-icon ui-icon-carat-1-s ui-float-right' : 'ui-icon ui-icon-carat-1-n ui-float-right';
			new HTMLSpan($link,'',$id.'_'.$this->sortDir,$curArrow);
		} else {
			$link = new HTMLAnchor($parentElement,"#",'',$id,'txn_title');
		}
		new HTMLText($link,$title);
	}

	function buildNewTxns($tableTxn,$row) {
		$col = 0;
		$selectAcct = new HTMLSelect($tableTxn->cells[$row][$col++],'new_txn_acct','new_txn_acct','txn_input');
		$this->DB->resetRowPointer($this->activeAccounts);
		$selectedAcct = ($this->newTxnValues['acct']) ? $this->newTxnValues['acct'] : $this->showAcct;
		while($result = $this->DB->fetch($this->activeAccounts)) {
			$selected = ($selectedAcct == $result['id']) ? TRUE : FALSE; // Will select currently shown account
			new HTMLOption($selectAcct,$result['name'],$result['id'],$selected);
		}
		new HTMLText($tableTxn->cells[$row][$col++],'-');
		new HTMLText($tableTxn->cells[$row][$col++],'-');
		$txn_current_tag = new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_date',$this->newTxnValues['date'],'new_txn_date','dateselection txn_input');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_type\')');
		$txn_current_tag = new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_type',$this->newTxnValues['type'],'new_txn_type','txn_input');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_establishment\')');
		$txn_current_tag = new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_establishment',$this->newTxnValues['establishment'],'new_txn_establishment','txn_input');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_note\')');
		$txn_current_tag = new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_note',$this->newTxnValues['note'],'new_txn_note','txn_input');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_credit\')');
		$txn_current_tag = new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_credit',$this->newTxnValues['credit'],'new_txn_credit','txn_input credit');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_debit\')');
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_debit',$this->newTxnValues['debit'],'new_txn_debit','txn_input debit');
		new HTMLText($tableTxn->cells[$row][$col++],'-');
		new HTMLText($tableTxn->cells[$row][$col++],'-');
		$submitNew = new HTMLAnchor($tableTxn->cells[$row][$col++],'#','','txn_add');
		$submitNew->setTitle("Add");
		new HTMLSpan($submitNew,'','new_txn_submit','ui-icon ui-icon-plusthick');
	}

	function buildTxns($tableTxn,$row) {
		if ($this->activeTxns) {
			$currentBalance = $this->activeTxnsSum;
			$currentBankSays = $this->activeTxnsBankSaysSum;
			while($txn = $this->DB->fetch($this->activeTxns)) {
				$col = 0;
				$oddOrEven = ($row % 2 == 0) ? "odd" : "even";
				$selectAcct = new HTMLSelect($tableTxn->cells[$row][$col++],'txn_acct_'.$txn['id'],'txn_acct_'.$txn['id'],'txn_acct_select_'.$oddOrEven);
				$this->DB->resetRowPointer($this->activeAccounts);
				while($result = $this->DB->fetch($this->activeAccounts)) {
					$selected = ($txn['acct_id'] == $result['id']) ? TRUE : FALSE;
					new HTMLOption($selectAcct,$result['name'],$result['id'],$selected);
				}

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable');
				new HTMLText($tableTxn->cells[$row][$col++],$txn['handle']);

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable number');
				new HTMLText($tableTxn->cells[$row][$col++],date($this->user->getDateFormat(),$txn['entered']));

				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_date_'.$txn['id'],date($this->user->getDateFormat(),$txn['date']),'txn_date_'.$txn['id'],'dateselection txn_input number');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_type_'.$txn['id'],$txn['type'],'txn_type_'.$txn['id'],'txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_establishment_'.$txn['id'],$txn['establishment'],'txn_establishment_'.$txn['id'],'txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_note_'.$txn['id'],$txn['note'],'txn_note_'.$txn['id'],'txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_credit_'.$txn['id'],$txn['credit'],'txn_credit_'.$txn['id'],'txn_input number credit');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_debit_'.$txn['id'],$txn['debit'],'txn_debit_'.$txn['id'],'txn_input number debit');

				$posNeg = (round($currentBalance,2)>=0) ? ' positive' : ' negative';
				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' number'.$posNeg);
				new HTMLText($tableTxn->cells[$row][$col++],number_format(round($currentBalance,2),2));

				$posNeg = (round($currentBankSays,2)>=0) ? ' positive' : ' negative';
				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' number'.$posNeg);
				$checked = ($txn['banksays']) ? TRUE : FALSE;
				new HTMLInputCheckbox($tableTxn->cells[$row][$col],'txn_banksays_'.$txn['id'],'txn_banksays_'.$txn['id'],'txn_banksays_check',$checked);
				$parent_id = ($txn['parent_txn_id']) ? $txn['parent_txn_id'] : $txn['id'];
				new HTMLInputHidden($tableTxn->cells[$row][$col],'txn_parent_id_'.$txn['id'],$parent_id,'txn_parent_id_'.$txn['id']);
				new HTMLText($tableTxn->cells[$row][$col++],number_format(round($currentBankSays,2),2));

				$currentBalance = $currentBalance + $txn['debit'] - $txn['credit'];
				if ($checked) $currentBankSays = $currentBankSays + $txn['debit'] - $txn['credit'];

				if ($parent_id == $txn['parent_txn_id']) {
					$showHistory = new HTMLAnchor($tableTxn->cells[$row][$col],'#','','txn_show_history_anchor_'.$txn['id'],'txn_show_history');
					new HTMLSpan($showHistory,'','txn_show_history_'.$txn['id'],'ui-icon ui-icon-clock ui-float-left');
				} else {
					$showHistory = new HTMLAnchor($tableTxn->cells[$row][$col],'#','','');
					new HTMLSpan($showHistory,'','txn_show_history_'.$txn['id'],'ui-icon-inactive ui-icon-clock ui-float-left');
				}
				$showHistory->setTitle('History');

				$showNotes = new HTMLAnchor($tableTxn->cells[$row][$col],'#','','txn_show_notes_'.$txn['id'],'txn_show_notes');
				new HTMLSpan($showNotes,'','txn_show_notes_'.$txn['id'],'ui-icon ui-icon-note ui-float-left');
				$showNotes->setTitle('Notes');

				$saveTxn = new HTMLAnchor($tableTxn->cells[$row][$col],'#','','txn_save_anchor_'.$txn['id'],'');
				$saveTxn->setTitle('Save');
				new HTMLSpan($saveTxn,'','txn_save_'.$txn['id'],'ui-icon-inactive ui-icon-disk ui-float-right');

				$row++;
				$tableTxn->makeSingleCellRow($row);
				$tableTxn->removeRowAttribs($row);
				$tableTxn->rows[$row]->setAttribute('style','display:none;');
				$tableTxn->rows[$row]->setClass('txn_history_row');
				$tableTxn->rows[$row]->setId('txn_history_row_'.$txn['id']);
				$tableTxn->removeCellAttribs($row,0);
				$tableTxn->cells[$row][0]->setClass('txn_history');
				$tableTxn->cells[$row][0]->setId('txn_history_'.$txn['id']);

				$row++;
				$tableTxn->makeSingleCellRow($row);
				$tableTxn->removeRowAttribs($row);
				$tableTxn->rows[$row]->setAttribute('style','display:none;');
				$tableTxn->rows[$row]->setClass('txn_notes_row');
				$tableTxn->rows[$row]->setId('txn_notes_row_'.$txn['id']);
				$tableTxn->removeCellAttribs($row,0);
				$tableTxn->cells[$row][0]->setClass('txn_notes');
				$tableTxn->cells[$row][0]->setId('txn_notes_'.$txn['id']);
				new HTMLText($tableTxn->cells[$row][0],'NOTES testing testing testing testing testing testing testing testing testing testing testing ');
				$row++;
			}
		} else {
			$this->infoMsg->addMessage(-1,'There was a problem retrieving the transaction data.');
		}
	}

	function getTxns() {
		$sql = "SELECT q_txn.*,user.handle FROM q_txn,user,q_acct
					WHERE ({$this->getSqlAcctsToShow()})
					  AND q_txn.active = 1
					  AND q_txn.user_id = user.user_id
					  AND q_txn.acct_id = q_acct.id
					GROUP BY q_txn.id
					ORDER BY {$this->sortField} {$this->sortDir},q_txn.type ASC,q_txn.establishment ASC,q_txn.note ASC,q_txn.entered ASC;"; // Need to add next lvl search for consistent results
		return $this->activeTxns = $this->DB->query($sql);
	}

	function getTxnsSum() {
		return $this->activeTxnsSum = $this->getCreditSum() - $this->getDebitSum();
	}

	function getTxnsBankSaysSum() {
		return $this->activeTxnsBankSaysSum = $this->getCreditBankSaysSum() - $this->getDebitBankSaysSum();
	}

	function getCreditSum() {
		$sql = "SELECT SUM(q_txn.credit) AS total FROM q_txn
					WHERE ({$this->getSqlAcctsToShow()})
					  AND q_txn.active = 1;";
		$result = $this->DB->fetch($this->DB->query($sql));
		return $result['total'];
	}

	function getCreditBankSaysSum() {
		$sql = "SELECT SUM(q_txn.credit) AS total FROM q_txn
					WHERE ({$this->getSqlAcctsToShow()})
					  AND q_txn.active = 1
					  AND q_txn.banksays = 1;";
		$result = $this->DB->fetch($this->DB->query($sql));
		return $result['total'];
	}

	function getDebitSum() {
		$sql = "SELECT SUM(q_txn.debit) AS total FROM q_txn
					WHERE ({$this->getSqlAcctsToShow()})
					  AND q_txn.active = 1;";
		$result = $this->DB->fetch($this->DB->query($sql));
		return $result['total'];
	}

	function getDebitBankSaysSum() {
		$sql = "SELECT SUM(q_txn.debit) AS total FROM q_txn
					WHERE ({$this->getSqlAcctsToShow()})
					  AND q_txn.active = 1
					  AND q_txn.banksays = 1;";
		$result = $this->DB->fetch($this->DB->query($sql));
		return $result['total'];
	}

	function getSqlAcctsToShow() {
		if ($this->showAcct) {
			$acctsToShow = "q_txn.acct_id = ".$this->showAcct;
		} else {
			$i = 0;
			$this->DB->resetRowPointer($this->activeAccounts);
			while($result = $this->DB->fetch($this->activeAccounts)) {
				if ($i == 0 ) $acctsToShow = "";
				elseif ($i < $this->DB->num($this->activeAccounts)) $acctsToShow .= " OR ";
				$acctsToShow .= "q_txn.acct_id = ".$result['id'];
				$i++;
			}
		}
		return $acctsToShow;
	}

	function insertTxn($acct,$user_id,$date,$type,$establishment,$note,$credit,$debit,$parent_id,$banksays) {
		if (!empty($credit) and !empty($debit)) {
			$this->infoMsg->addMessage(-1,'Credit and debit have values, this transaction cannot be added.');
			return false;
		} elseif (empty($credit) and empty($debit)) {
			$this->infoMsg->addMessage(-1,'Credit and debit do not have values, this transaction cannot be added.');
			return false;
		} else {
			$txn_type = (!empty($credit)) ? 'credit' : 'debit';
			$value = (!empty($credit)) ? $credit : $debit;
			$value = str_replace('$', '', $value);
			$banksays = ($banksays == 'on') ? 1 : 0;
			$entered = $this->user->getTime();
			$sql = "INSERT INTO q_txn (acct_id,user_id,entered,date,type,establishment,note,$txn_type,parent_txn_id,banksays,active)
				VALUES ($acct,$user_id,$entered,$date,'$type','$establishment','$note',$value,$parent_id,$banksays,1);";
			return $this->DB->query($sql);
		}
	}

	function addEntries($acct,$date,$type,$establishment,$note,$credit,$debit,$parent_id,$banksays,$current_txn_id) {
		if ($this->validateNewTxn($date,$credit,$debit)) {
			$date = strtotime($date);
			if ($this->insertTxn($acct,$this->user->getUserId(),$date,$type,$establishment,$note,$credit,$debit,$parent_id,$banksays)) {
				if ($current_txn_id != 'null') {
					if ($this->makeTxnInactive($current_txn_id))
					$this->infoMsg->addMessage(2,'Transaction was successfully modified.');
				} else {
					$this->infoMsg->addMessage(2,'Transaction was successfully added.');
				}
			} else {
				$this->infoMsg->addMessage(-1,'An error occured while trying to add the transaction.');
			}
		} else {
			$this->newTxnValues['acct'] = $acct;
			$this->newTxnValues['date'] = $date;
			$this->newTxnValues['type'] = $type;
			$this->newTxnValues['establishment'] = $establishment;
			$this->newTxnValues['note'] = $note;
			$this->newTxnValues['credit'] = $credit;
			$this->newTxnValues['debit'] = $debit;
		}
	}

	function makeTxnInactive($current_txn_id) {
		$sql = "UPDATE q_txn SET active = 0 WHERE q_txn.id = $current_txn_id;";
		return $this->DB->query($sql);
	}

	function validateNewTxn($date,$credit,$debit) {
		if (!strtotime($date)) {
			$this->infoMsg->addMessage(0,'Date was incorrectly formatted.');
			return FALSE;
		} elseif (empty($credit) and empty($debit)) {
			$this->infoMsg->addMessage(0,'Credit or Debit require a value.');
			return FALSE;
		} elseif (!empty($credit) and !empty($debit)) {
			$this->infoMsg->addMessage(0,'Credit and Debit cannot both have values.');
			return FALSE;
		} elseif (!(Normalize::validateCash($credit) and Normalize::validateCash($debit))) {
			$txnType = (!empty($credit)) ? 'Credit' : 'Debit';
			$this->infoMsg->addMessage(0,$txnType.' value is invalid.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function buildTxnHistoryTable($txn_id) {
		$this->getActiveAccounts();
		$this->getTxnHistory($txn_id);
		$rows = $this->DB->num($this->txnHistory);
		$tableTxn = new Table($this->container,$rows + 1,11,'txnh_table_'.$txn_id,'txnh');
		$this->buildTxnHistoryTitles($tableTxn,$row = 0);
		$this->buildTxnHistory($tableTxn,++$row);
		$this->printHTML();
	}

	function buildTxnHistory($tableTxn,$row = 0) {
		if ($this->txnHistory) {
			while($txn = $this->DB->fetch($this->txnHistory)) {
				$col = 0;
				$oddOrEven = ($row % 2 == 0) ? "odd" : "even";
				$current_txn = ($row == 1) ? " current_txn" : "";
				$selectAcct = new HTMLSelect($tableTxn->cells[$row][$col++],'txnh_acct_'.$txn['id'],'txnh_acct_'.$txn['id'],'txnh_acct_select_'.$oddOrEven.$current_txn);
				$this->DB->resetRowPointer($this->activeAccounts);
				while($result = $this->DB->fetch($this->activeAccounts)) {
					$selected = ($txn['acct_id'] == $result['id']) ? TRUE : FALSE;
					new HTMLOption($selectAcct,$result['name'],$result['id'],$selected);
				}

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable');
				new HTMLText($tableTxn->cells[$row][$col++],$txn['handle']);

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable number');
				new HTMLText($tableTxn->cells[$row][$col++],date($this->user->getDateFormat().' g:i:s a',$txn['entered']));

				new HTMLInputText($tableTxn->cells[$row][$col++],'txnh_date_'.$txn['id'],date($this->user->getDateFormat(),$txn['date']),'txnh_date_'.$txn['id'],'dateselection txn_input number');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txnh_type_'.$txn['id'],$txn['type'],'txnh_type_'.$txn['id'],'txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txnh_establishment_'.$txn['id'],$txn['establishment'],'txnh_establishment_'.$txn['id'],'txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txnh_note_'.$txn['id'],$txn['note'],'txnh_note_'.$txn['id'],'txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txnh_credit_'.$txn['id'],$txn['credit'],'txnh_credit_'.$txn['id'],'txn_input number credit');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txnh_debit_'.$txn['id'],$txn['debit'],'txnh_debit_'.$txn['id'],'txn_input number debit');

				$checked = ($txn['banksays']) ? TRUE : FALSE;
				$checkBox = new HTMLInputCheckbox($tableTxn->cells[$row][$col],'txnh_banksays_'.$txn['id'],'txnh_banksays_'.$txn['id'],'txnh_banksays_check',$checked);
				$checkBox->setAttribute('disabled','disabled');
				$parent_id = ($txn['parent_txn_id']) ? $txn['parent_txn_id'] : $txn['id'];
				new HTMLInputHidden($tableTxn->cells[$row][$col++],'txnh_parent_id_'.$txn['id'],$parent_id,'txnh_parent_id_'.$txn['id']);

				if ($row == 1) {
					for($i=0;$i<=$col;$i++) {
						$tableTxn->cells[$row][$i]->setClass($tableTxn->cells[$row][$i]->getClass().$current_txn);
					}
				} else {
					$makeActive = new HTMLAnchor($tableTxn->cells[$row][$col],'#','','txn_make_active');
					$makeActive->setTitle('Make Active');
					new HTMLSpan($makeActive,'','txn_make_active_'.$txn['id'],'ui-icon ui-icon-arrowreturnthick-1-n ui-float-left');
				}

				$row++;
			}
		} else {
			$this->infoMsg->addMessage(-1,'There was a problem retrieving the transaction data.');
		}
	}

	function buildTxnHistoryTitles($tableTxn,$row = 0) {
		$col = 0;

		new HTMLText($tableTxn->cells[$row][$col++],'Account');
		new HTMLText($tableTxn->cells[$row][$col++],'User');
		new HTMLText($tableTxn->cells[$row][$col++],'Date Entered');
		new HTMLText($tableTxn->cells[$row][$col++],'Transaction Date');
		new HTMLText($tableTxn->cells[$row][$col++],'Type');
		new HTMLText($tableTxn->cells[$row][$col++],'Establishment');
		new HTMLText($tableTxn->cells[$row][$col++],'Note');
		new HTMLText($tableTxn->cells[$row][$col++],'Credit');
		new HTMLText($tableTxn->cells[$row][$col++],'Debit');
		new HTMLText($tableTxn->cells[$row][$col++],'Bank');
		new HTMLText($tableTxn->cells[$row][$col++],'Actions');
	}

}
?>