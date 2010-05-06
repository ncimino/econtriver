<?php
class AjaxQaTxns extends AjaxQaWidget {
	private $activeAccounts; // MySQL result
	private $activeTxns; // MySQL result
	private $newTxnValues = array();
	private $displayAcct = 0;

	function __construct($parentId) {
		parent::__construct();
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		} else {
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
		$rows = $this->DB->num($this->activeTxns) + 2;
		$tableTxn = new Table($divNew,$rows,12,'txn_table','txn');
		$this->buildTxnTitles($tableTxn,0);
		$this->buildNewTxns($tableTxn,1);
		$this->buildTxns($tableTxn,2);
	}

	function buildTxnTitles($tableTxn,$row) {
		$col = 0;
		$sortDir = (isset($_GET['sortDir']) and $_GET['sortDir'] == 'DESC' ) ? 'ASC' : 'DESC';
		$sortId = (!isset($_GET['sortId'])) ? 'q_txn.date' : $_GET['sortId'];

		if ($sortId == 'q_acct.name') {
			$curSortDir = $sortDir;
			$aAccount = new HTMLAnchor($tableTxn->cells[$row][$col],'index.php?sortId=q_acct.name&sortDir='.$curSortDir,'');
			$curArrow = ($sortDir == 'DESC') ? 'ui-icon ui-icon-carat-1-s ui-float-right' : 'ui-icon ui-icon-carat-1-n ui-float-right';
			new HTMLSpan($tableTxn->cells[$row][$col++],'','',$curArrow);
		} else {
			$curSortDir = 'DESC';
			$aAccount = new HTMLAnchor($tableTxn->cells[$row][$col++],'index.php?sortId=q_acct.name&sortDir='.$curSortDir,'');
		}
		new HTMLText($aAccount,'Account');

		new HTMLText($tableTxn->cells[$row][$col],'User');
		$test = new HTMLSpan($tableTxn->cells[$row][$col++],'','','ui-icon ui-icon-carat-1-n');
		$test->setStyle('float:right;');

		$aDateEntered = new HTMLAnchor($tableTxn->cells[$row][$col++],'index.php?sortId=q_txn.entered&sortDir='.$sortDir,'');
		new HTMLText($aDateEntered,'Date Entered');

		$aTxnDate = new HTMLAnchor($tableTxn->cells[$row][$col++],'index.php?sortId=q_txn.date&sortDir='.$sortDir,'');
		new HTMLText($aTxnDate,'Transaction Date');

		new HTMLText($tableTxn->cells[$row][$col++],'Type');
		new HTMLText($tableTxn->cells[$row][$col++],'Establishment');
		new HTMLText($tableTxn->cells[$row][$col++],'Note');
		new HTMLText($tableTxn->cells[$row][$col++],'Credit');
		new HTMLText($tableTxn->cells[$row][$col++],'Debit');
		new HTMLText($tableTxn->cells[$row][$col++],'Balance');
		new HTMLText($tableTxn->cells[$row][$col++],'Bank Says');
		new HTMLText($tableTxn->cells[$row][$col++],'Actions');
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
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_date',$this->newTxnValues['date'],'new_txn_date','dateselection');
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_type',$this->newTxnValues['type'],'new_txn_type');
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_establishment',$this->newTxnValues['establishment'],'new_txn_establishment');
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_note',$this->newTxnValues['note'],'new_txn_note');
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_credit',$this->newTxnValues['credit'],'new_txn_credit');
		new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_debit',$this->newTxnValues['debit'],'new_txn_debit');
		new HTMLText($tableTxn->cells[$row][$col++],'-');
		new HTMLText($tableTxn->cells[$row][$col++],'-');
		$submitNew = new HTMLAnchor($tableTxn->cells[$row][$col++],'#','','txn_add');
		new HTMLSpan($submitNew,'','new_txn_submit','ui-icon ui-icon-plusthick');
	}

	function buildTxns($tableTxn,$row) {
		if ($this->activeTxns) {
			while($txn = $this->DB->fetch($this->activeTxns)) {
				$col = 0;
				$selectAcct = new HTMLSelect($tableTxn->cells[$row][$col++],'txn_acct','txn_acct');
				$this->DB->resetRowPointer($this->activeAccounts);
				while($result = $this->DB->fetch($this->activeAccounts)) {
					$selected = ($txn['acct_id'] == $result['id']) ? TRUE : FALSE; // Will select currently shown account
					new HTMLOption($selectAcct,$result['name'],$result['id'],$selected);
				}
				new HTMLText($tableTxn->cells[$row][$col++],$txn['handle']);
				new HTMLText($tableTxn->cells[$row][$col++],date($this->user->getDateFormat(),strtotime($txn['entered'])));
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_date',date($this->user->getDateFormat(),$txn['date']),'txn_date','dateselection');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_type',$txn['type'],'txn_type');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_establishment',$txn['establishment'],'txn_establishment');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_note',$txn['note'],'txn_note');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_credit',$txn['credit'],'txn_credit');
				new HTMLInputText($tableTxn->cells[$row][$col++],'txn_debit',$txn['debit'],'txn_debit');
				/*
				new HTMLText($tableTxn->cells[$row][$col++],'-');
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
		if ($this->displayAcct) {
			$sql = "SELECT q_txn.*,user.handle FROM q_txn,q_owners,q_user_groups,q_share,q_acct,user
					WHERE ( 
						  q_txn.acct_id = q_share.acct_id
					  AND q_acct.id = {$this->displayAcct}
					  AND q_user_groups.group_id = q_share.group_id
					  AND q_user_groups.user_id = user.user_id
					  AND q_txn.user_id = q_user_groups.user_id
					  AND q_share.acct_id = q_acct.id
					  AND q_user_groups.active = 1 
					  AND q_acct.active = 1 
					  AND q_user_groups.active = 1  
					  ) OR (
					  	  q_txn.acct_id = q_owners.acct_id
					  AND q_owners.owner_id = {$this->user->getUserId()}
					  AND q_owners.owner_id = user.user_id
					  AND q_acct.active = 1
					  )
					GROUP BY q_txn.id
					ORDER BY q_txn.date DESC;";
			return $this->activeTxns = $this->DB->query($sql);
		} else {
			// This is not returning the correct users
			$sql = "SELECT q_txn.*,user.handle FROM q_txn,q_owners,q_user_groups,q_share,q_acct,user
					WHERE ( 
						  q_txn.acct_id = q_share.acct_id
					  AND q_user_groups.group_id = q_share.group_id
					  AND q_user_groups.user_id = user.user_id
					  AND q_txn.user_id = q_user_groups.user_id
					  AND q_share.acct_id = q_acct.id
					  AND q_user_groups.active = 1 
					  AND q_acct.active = 1 
					  AND q_user_groups.active = 1 
					  ) OR (
					  	  q_txn.acct_id = q_owners.acct_id
					  AND q_owners.owner_id = {$this->user->getUserId()}
					  AND q_owners.owner_id = user.user_id
					  AND q_acct.active = 1
					  )
					GROUP BY q_txn.id
					ORDER BY q_txn.date DESC;";
			echo $sql;
			return $this->activeTxns = $this->DB->query($sql);
		}
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