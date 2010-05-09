<?php
class AjaxQaTxns extends AjaxQaWidget {
	private $activeAccounts; // MySQL result
	private $activeTxns; // MySQL result
	private $activeTxnsSum = 0; // MySQL result
	private $newTxnValues = array();
	private $displayAcct = 0;
	private $sortDir = 'DESC';
	private $sortField = 'q_txn.date';

	function __construct($parentId,$sortId=NULL,$sortDir=NULL) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		} else {
			if (isset($sortDir)) $this->sortDir = $sortDir;
			if (isset($sortId)) $this->sortField = $this->convIdToField($sortId);
			$this->newTxnValues['date'] = date($this->user->getDateFormat(),$this->user->getTime());
			$this->newTxnValues['type'] = '';
			$this->newTxnValues['establishment'] = '';
			$this->newTxnValues['note'] = '';
			$this->newTxnValues['credit'] = '';
			$this->newTxnValues['debit'] = '';
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
		$selectAcct = new HTMLSelect($divActions,'show_acct','show_acct_id');
		new HTMLOption($selectAcct,'All Accounts',0);
		while($result = $this->DB->fetch($this->activeAccounts)) {
			new HTMLOption($selectAcct,$result['name'],$result['id']);
		}
	}

	function buildTxnsTable() {
		$divNew = new HTMLDiv($this->container,'txn_id','txn');
		$this->getTxns();
		$this->getTxnsSum();
		$rows = $this->DB->num($this->activeTxns) + 2;
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
		$selectAcct = new HTMLSelect($tableTxn->cells[$row][$col++],'new_txn_acct','new_txn_acct');
		$this->DB->resetRowPointer($this->activeAccounts);
		while($result = $this->DB->fetch($this->activeAccounts)) {
			$selected = ($this->newTxnValues['acct'] = $this->displayAcct) ? TRUE : FALSE; // Will select currently shown account
			new HTMLOption($selectAcct,$result['name'],$result['id'],$selected);
		}
		new HTMLText($tableTxn->cells[$row][$col++],'-');
		new HTMLText($tableTxn->cells[$row][$col++],'-');
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_date',$this->newTxnValues['date'],'new_txn_date','dateselection txn_input');
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_type',$this->newTxnValues['type'],'new_txn_type','txn_input');
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_establishment',$this->newTxnValues['establishment'],'new_txn_establishment','txn_input');
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_note',$this->newTxnValues['note'],'new_txn_note','txn_input');
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_credit',$this->newTxnValues['credit'],'new_txn_credit','txn_input');
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_debit',$this->newTxnValues['debit'],'new_txn_debit','txn_input');
		new HTMLText($tableTxn->cells[$row][$col++],'-');
		new HTMLText($tableTxn->cells[$row][$col++],'-');
		$submitNew = new HTMLAnchor($tableTxn->cells[$row][$col++],'#','','txn_add');
		new HTMLSpan($submitNew,'','new_txn_submit','ui-icon ui-icon-plusthick');
	}

	function buildTxns($tableTxn,$row) {
		if ($this->activeTxns) {
			$currentBalance = $this->activeTxnsSum;
			echo $this->activeTxnsSum;
			while($txn = $this->DB->fetch($this->activeTxns)) {
				$col = 0;
				$selectAcct = new HTMLSelect($tableTxn->cells[$row][$col++],'txn_acct','txn_acct');
				$this->DB->resetRowPointer($this->activeAccounts);
				while($result = $this->DB->fetch($this->activeAccounts)) {
					$selected = ($txn['acct_id'] == $result['id']) ? TRUE : FALSE;
					new HTMLOption($selectAcct,$result['name'],$result['id'],$selected);
				}
				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable');
				new HTMLText($tableTxn->cells[$row][$col++],$txn['handle']);
				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable');
				new HTMLText($tableTxn->cells[$row][$col++],date($this->user->getDateFormat(),strtotime($txn['entered'])));
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_date',date($this->user->getDateFormat(),$txn['date']),'txn_date','dateselection txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_type',$txn['type'],'txn_type','txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_establishment',$txn['establishment'],'txn_establishment','txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_note',$txn['note'],'txn_note','txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_credit',$txn['credit'],'txn_credit','txn_input');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_debit',$txn['debit'],'txn_debit','txn_input');
				new HTMLText($tableTxn->cells[$row][$col++],$currentBalance);
				$currentBalance = $currentBalance + $txn['debit'] - $txn['credit'];
				/*
				 new HTMLText($tableTxn->cells[$row][$col++],'-');
				 $submitNew = new HTMLAnchor($tableTxn->cells[$row][$col++],'#','','txn_add');
				 new HTMLSpan($submitNew,'','new_txn_submit','ui-icon ui-icon-plusthick');
				 */
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
					ORDER BY {$this->sortField} {$this->sortDir};";
		return $this->activeTxns = $this->DB->query($sql);
	}
	
	function getTxnsSum() {
		return $this->activeTxnsSum = $this->getCreditSum() - $this->getDebitSum();
	}

	function getCreditSum() {
		$sql = "SELECT SUM(q_txn.credit) AS total FROM q_txn
					WHERE ({$this->getSqlAcctsToShow()})
					  AND q_txn.active = 1;";
		$result = $this->DB->fetch($this->DB->query($sql));
		echo 'credit:'.$result['total'];
		return $result['total'];
	}
	
	function getDebitSum() {
		$sql = "SELECT SUM(q_txn.debit) AS total FROM q_txn
					WHERE ({$this->getSqlAcctsToShow()})
					  AND q_txn.active = 1;";
		$result = $this->DB->fetch($this->DB->query($sql));
		echo 'debit:'.$result['total'];
		return $result['total'];
	}

	function getSqlAcctsToShow() {
		if ($this->displayAcct) {
			$acctsToShow = "q_txn.acct_id = ".$this->displayAcct;
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

	function insertTxn($acct,$user_id,$date,$type,$establishment,$note,$credit,$debit) {
		if (!empty($credit) and !empty($debit)) {
			$this->infoMsg->addMessage(-1,'Credit and debit have values, this transaction cannot be added.');
			return false;
		} elseif (empty($credit) and empty($debit)) {
			$this->infoMsg->addMessage(-1,'Credit and debit do not have values, this transaction cannot be added.');
			return false;
		} else {
			$txn_type = (!empty($credit)) ? 'credit' : 'debit';
			$value = (!empty($credit)) ? $credit : $debit;
			$sql = "INSERT INTO q_txn (acct_id,user_id,date,type,establishment,note,$txn_type,active)
				VALUES ($acct,$user_id,$date,'$type','$establishment','$note',$value,1);";
			return $this->DB->query($sql);
		}
	}

	function addEntries($acct,$date,$type,$establishment,$note,$credit,$debit) {
		if ($this->validateNewTxn($date,$credit,$debit)) {
			$date = strtotime($date);
			if ($this->insertTxn($acct,$this->user->getUserId(),$date,$type,$establishment,$note,$credit,$debit)) {
				$this->infoMsg->addMessage(2,'Transaction was successfully added.');
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

}
?>