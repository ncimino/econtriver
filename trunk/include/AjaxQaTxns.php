<?php
class AjaxQaTxns extends AjaxQaWidget {
	protected $activeAccounts; // MySQL result
	protected $ownedAccounts; // MySQL result
	protected $sqlAcctsToShow; // SQL syntax

	protected $activeTxns; // MySQL result
	protected $activeTxnsSum; // MySQL result
	protected $activeTxnsBankSaysSum; // MySQL result

	protected $newTxnValues = array();
	protected $sortDir = 'DESC';
	protected $sortField = 'q_txn.date';
	protected $showAcct = "0";

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
			$this->newTxnValues['acct'] = "0";
			$this->newTxnValues['date'] = date($this->user->getDateFormat(),$this->user->getTime());
			$this->newTxnValues['type'] = '';
			$this->newTxnValues['establishment'] = '';
			$this->newTxnValues['note'] = '';
			$this->newTxnValues['credit'] = '';
			$this->newTxnValues['debit'] = '';
		}
	}

	function setShowAcct($acctId) {
		if (QaSettings::setSetting('show_acct',$this->user->getUserId(),$acctId,$this->DB)) {
			$this->showAcct = $acctId;
		}
	}

	function getShowAcct() {
		$this->showAcct = QaSettings::getSetting('show_acct',$this->user->getUserId(),$this->DB);
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
			case 'q_txn.banksays':			    $return = 'txn_title_banksays'; break;
			case 'txn_title_banksays' :			$return = 'q_txn.banksays'; break;
		}
		return $return;
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

	function getTxnParentId($current_txn_id) {
		$sql = "SELECT parent_txn_id FROM q_txn
					WHERE id = $current_txn_id;";
		$result = $this->DB->fetch($this->DB->query($sql));
		return $result['parent_txn_id'];
	}

	function dropEntries($active_txn_id, $log, $current_txn_id=FALSE) {
		if ($this->makeTxnInactive($active_txn_id)) {
			if ($current_txn_id) { // If current_txn_id is defined, then the transaction was made active
				// This occurs when restoring a previous revision of the same transaction. The current must be made inactive.
				//$this->infoMsg->addMessage(2,'Transaction was successfully made active.');
			} else {
				$this->infoMsg->addMessage(2,'Transaction was successfully moved to the trash bin.');
				if ($log == 'true') $this->insertTxnNote($this->getTxnParentId($active_txn_id), "Deleted Transaction", FALSE);
			}
		} else {
			$this->infoMsg->addMessage(-1,'An unexpected error occurred while trying to move the transaction to the trash.');
		}
	}

	function makeTxnInactive($current_txn_id) {
		$sql = "UPDATE q_txn SET active = 0 WHERE q_txn.id = $current_txn_id;";
		return $this->DB->query($sql);
	}

	function restoreEntries($current_txn_id) {
		if ($this->makeTxnActive($current_txn_id)) {
			$this->infoMsg->addMessage(2,'Transaction was successfully restored.');
			$this->insertTxnNote($this->getTxnParentId($current_txn_id), "Restored Transaction", FALSE);
		} else {
			$this->infoMsg->addMessage(-1,'An unexpected error occurred while trying to restore the transaction.');
		}
	}

	function makeTxnActive($current_txn_id) {
		$sql = "UPDATE q_txn SET active = 1 WHERE q_txn.id = $current_txn_id;";
		return $this->DB->query($sql);
	}

	private function insertTxnNote($parent_txn_id, $note, $editable=TRUE) {
		$edited = ($editable) ? "null" : "1";
		$clean_note = Normalize::tags($note);
		$sql = "INSERT INTO q_txn_notes (user_id,txn_id,posted,note,edited)
				VALUES ({$this->user->getUserId()}, $parent_txn_id, {$this->user->getTime()}, '$clean_note', $edited);";
		return $this->DB->query($sql);
	}

	function buildWidget() {
		$this->activeAccounts = AjaxQaSelectAccounts::getActiveAccounts($this->user->getUserId(),$this->DB);
		$this->ownedAccounts = AjaxQaSelectAccounts::getOwnedAccounts($this->user->getUserId(),$this->DB);
		$this->sqlAcctsToShow = AjaxQaSelectAccounts::getSqlAcctsToShow($this->showAcct,$this->activeAccounts,$this->user->getUserId(),$this->DB);
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

		$jsDateFormat = ($this->user->getDateFormat() == "Y-m-d") ? "yy-mm-dd" : "mm/dd/yy"; // else "m/d/Y"
		new HTMLInputHidden($divActions,'date_format',$jsDateFormat,'date_format');

		$h3View = new HTMLHeading($divActions,5,'View Account: ');
		$h3View->setStyle('float: left;margin: 4px;padding 0px;');

		$this->buildAcctsDropDown($divActions,'show_acct','show_acct',NULL,$this->showAcct,$userSelectable=TRUE,$allAccounts=TRUE);

		$divActionButtons = new HTMLDiv($divActions,'txn_actions_buttons');
		$divActionButtons->setStyle('float: right;');

		$h3Actions = new HTMLHeading($divActionButtons,5,'Actions: ');
		$h3Actions->setStyle('float: left;margin: 2px;padding 0px;');

		$trashTxn = new HTMLAnchor($divActionButtons,'#','','.txn_show_trash_anchor','txn_show_trash');
		$trashTxn->setTitle('Trash Bin');
		new HTMLSpan($trashTxn,'','txn_show_trash','ui-icon ui-icon-trash ui-float-right');

		$printTxn = new HTMLAnchor($divActionButtons,'#','','txn_print_anchor');
		$printTxn->setTitle('Open Print View');
		new HTMLSpan($printTxn,'','txn_print','ui-icon-inactive ui-icon-print ui-float-right');

		$divActionContent = new HTMLDiv($divActions,'txn_actions_content');
		$divActionContent->setAttribute('style','display:none;');
	}

	function buildAcctsDropDown($parentElement,$name=NULL,$id=NULL,$class=NULL,$selectedAcct=NULL,$userSelectable=FALSE,$allAccounts=FALSE) {
		$allAccountsIndex = 0;
		$selectAcctMenu = new DropDownMenu($parentElement,$name,$id,$class);
		if ($allAccounts) $selectAcctMenu->addOption('All Accounts',$allAccountsIndex,($allAccountsIndex === $selectedAcct));
		$accountSelected = $this->buildGroupOfAcctsForDropDown($selectAcctMenu,'My Accounts',$this->user->getUserId(),'u'.$this->user->getUserId(),$selectedAcct,$userSelectable);
		$activeContacts = AjaxQaSelectGroupMembers::getAssociatedActiveContactsForAllGroups($this->user->getUserId(),$this->DB);
		while($result = $this->DB->fetch($activeContacts)) {
			if ($this->user->getUserId() != $result['user_id']) {
				$this->buildGroupOfAcctsForDropDown($selectAcctMenu,$result['handle'].'\'s Accounts',$result['user_id'],'u'.$result['user_id'],$selectedAcct,$userSelectable);
			}
		}
		if ($userSelectable) {
			$firstAccount = ($allAccounts) ? 2 : 1;
			$selectAcctMenu->setSelected($firstAccount);
		}
	}

	function buildGroupOfAcctsForDropDown($selectAcctMenu,$groupName,$userId,$groupId,$selectedAcct,$userSelectable) {
		$allAccountsIndex = 0;
		$userGroupSelected = ($userSelectable) ? ($selectedAcct==$groupId) : FALSE;
		$selectAcctMenu->addOption($groupName,$groupId,$userGroupSelected);
		if (!$userSelectable) $selectAcctMenu->disableOption();
		$indent = ($userSelectable) ? '&nbsp;&nbsp;' : '';
		$this->DB->resetRowPointer($this->activeAccounts);
		while($result = $this->DB->fetch($this->activeAccounts)) {
			if ($result['owner_id'] == $userId) {
				$selectAcctMenu->addOption($indent.$result['name'],$result['id'],($selectedAcct==$result['id']));
			}
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

	function getTxns() {
		$sql = "SELECT q_txn.*,user.handle FROM q_txn,user,q_acct
					WHERE (".$this->sqlAcctsToShow.")
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
					WHERE (".$this->sqlAcctsToShow.")
					  AND q_txn.active = 1;";
		$result = $this->DB->fetch($this->DB->query($sql));
		return $result['total'];
	}

	function getCreditBankSaysSum() {
		$sql = "SELECT SUM(q_txn.credit) AS total FROM q_txn
					WHERE (".$this->sqlAcctsToShow.")
					  AND q_txn.active = 1
					  AND q_txn.banksays = 1;";
		$result = $this->DB->fetch($this->DB->query($sql));
		return $result['total'];
	}

	function getDebitSum() {
		$sql = "SELECT SUM(q_txn.debit) AS total FROM q_txn
					WHERE (".$this->sqlAcctsToShow.")
					  AND q_txn.active = 1;";
		$result = $this->DB->fetch($this->DB->query($sql));
		return $result['total'];
	}

	function getDebitBankSaysSum() {
		$sql = "SELECT SUM(q_txn.debit) AS total FROM q_txn
					WHERE (".$this->sqlAcctsToShow.")
					  AND q_txn.active = 1
					  AND q_txn.banksays = 1;";
		$result = $this->DB->fetch($this->DB->query($sql));
		return $result['total'];
	}

	function buildTxnTitles($tableTxn,$row) {
		$col = 0;

		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Account','q_acct.name',$this->convIdToField('q_acct.name'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'User','user.handle',$this->convIdToField('user.handle'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Entered','q_txn.entered',$this->convIdToField('q_txn.entered'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Date','q_txn.date',$this->convIdToField('q_txn.date'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Type','q_txn.type',$this->convIdToField('q_txn.type'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Establishment','q_txn.establishment',$this->convIdToField('q_txn.establishment'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Note','q_txn.note',$this->convIdToField('q_txn.note'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Credit','q_txn.credit',$this->convIdToField('q_txn.credit'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Debit','q_txn.debit',$this->convIdToField('q_txn.debit'));
		new HTMLText($tableTxn->cells[$row][$col++],'Balance');
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Bank Says','q_txn.banksays',$this->convIdToField('q_txn.banksays')); //:BUG:Fixes issue 42
		new HTMLText($tableTxn->cells[$row][$col++],'Actions');
	}

	function buildNewTxns($tableTxn,$row) {
		$col = 0;
		$selectedAcct = ($this->newTxnValues['acct']) ? $this->newTxnValues['acct'] : $this->showAcct; // If a txn was entered use that
		$this->buildAcctsDropDown($tableTxn->cells[$row][$col++],'new_txn_acct','new_txn_acct','txn_input',$selectedAcct);
		new HTMLText($tableTxn->cells[$row][$col++],'-');
		new HTMLText($tableTxn->cells[$row][$col++],'-');
		$txn_current_tag = new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_date',$this->newTxnValues['date'],'new_txn_date','dateselection txn_input');
		// Seems to be breaking datepicker return=tab
		//$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_type\')');
		// This doesn't work as a workaround
		//$txn_current_tag->setAttribute('onchange',"focus('new_txn_type')");
		$txn_current_tag = new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_type',$this->newTxnValues['type'],'new_txn_type','txn_input autocomplete_type');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_establishment\')');
		$txn_current_tag = new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_establishment',$this->newTxnValues['establishment'],'new_txn_establishment','txn_input autocomplete_establishment');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_note\')');
		$txn_current_tag = new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_note',$this->newTxnValues['note'],'new_txn_note','txn_input autocomplete_note');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_credit\')');
		$txn_current_tag = new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_credit',$this->newTxnValues['credit'],'new_txn_credit','txn_input credit');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_debit\')');
		$txn_current_tag = new HTMLInputText($tableTxn->cells[$row][$col++],'new_txn_debit',$this->newTxnValues['debit'],'new_txn_debit','txn_input debit');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'txn_add\')');
		$txn_current_tag->setAttribute('tabindex','100'); // Need to build class for handling tabindex
		new HTMLText($tableTxn->cells[$row][$col++],'-'); // Balance
		new HTMLText($tableTxn->cells[$row][$col++],'-'); // Bank Says
		$submitNew = new HTMLAnchor($tableTxn->cells[$row][$col],'#','','txn_add');
		$submitNew->setAttribute('onkeyup','enterCall(event,function() {QaTxnAdd(\'new_txn_\');})');
		$submitNew->setTitle("Add");

		$submitNewSpan = new HTMLSpan($submitNew,'','new_txn_submit','ui-icon ui-icon-plusthick ui-float-left');
		$submitNewSpan->setAttribute('tabindex','101');

		$splitNew = new HTMLAnchor($tableTxn->cells[$row][$col],'#','','txn_split');
		//$splitNew->setAttribute('onkeyup','enterCall(event,function() {QaTxnAdd(\'new_txn_\');})');
		$splitNew->setTitle("Split");
		new HTMLSpan($splitNew,'','new_txn_split','ui-icon-inactive ui-icon-transferthick-e-w ui-float-left');
	}

	function buildTxns($tableTxn,$row) {
		if ($this->activeTxns) {
			$currentBalance = $this->activeTxnsSum;
			$currentBankSays = $this->activeTxnsBankSaysSum;
			while($txn = $this->DB->fetch($this->activeTxns)) {
				$col = 0;
				$oddOrEven = ($row % 2 == 0) ? "odd" : "even";

				$this->buildAcctsDropDown($tableTxn->cells[$row][$col++],'txn_acct_'.$txn['id'],'txn_acct_'.$txn['id'],'txn_acct_select_'.$oddOrEven,$txn['acct_id']);

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
				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' number balance'.$posNeg);
				new HTMLText($tableTxn->cells[$row][$col++],number_format(round($currentBalance,2),2));

				$posNeg = (round($currentBankSays,2)>=0) ? ' positive' : ' negative';
				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' number bank_says'.$posNeg);
				$checked = ($txn['banksays']) ? TRUE : FALSE;
				new HTMLInputCheckbox($tableTxn->cells[$row][$col],'txn_banksays_'.$txn['id'],'txn_banksays_'.$txn['id'],'txn_banksays_check',$checked);
				$parent_id = ($txn['parent_txn_id']) ? $txn['parent_txn_id'] : $txn['id'];
				new HTMLText($tableTxn->cells[$row][$col++],number_format(round($currentBankSays,2),2));

				$currentBalance = $currentBalance + $txn['debit'] - $txn['credit'];
				if ($checked) $currentBankSays = $currentBankSays + $txn['debit'] - $txn['credit'];

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' per_txn_actions');
				if ($txn['id'] != $txn['parent_txn_id']) {
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
				new HTMLSpan($saveTxn,'','txn_save_'.$txn['id'],'ui-icon-inactive ui-icon-disk ui-float-left');

				$deleteTxn = new HTMLAnchor($tableTxn->cells[$row][$col],'#','','txn_delete_anchor_'.$txn['id'],'txn_delete');
				$deleteTxn->setTitle('Delete');
				new HTMLSpan($deleteTxn,'','txn_delete_'.$txn['id'],'ui-icon ui-icon-trash ui-float-left');

				new HTMLInputHidden($tableTxn->cells[$row][$col],'txn_parent_id_'.$txn['id'],$parent_id,'txn_parent_id_'.$txn['id']);

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

				$row++;
			}
		} else {
			$this->infoMsg->addMessage(-1,'There was a problem retrieving the transaction data.');
		}
	}

	function addEntries($acct,$date,$type,$establishment,$note,$credit,$debit,$parent_id,$banksays,$current_txn_id,$active_txn_id=FALSE) {
		$return=FALSE;
		if ($this->validateNewTxn($date,$credit,$debit)) {
			$datestamp = strtotime($date);
			$this->getTxnActiveStatus($current_txn_id);
			$txnActiveStatus = $this->DB->fetch();
			if (($this->DB->num() >= 1) and ($txnActiveStatus['active'] == 0)) { // If current txn id exists, but is inactive
				$this->infoMsg->addMessage(0,'This entry was modified before you submitted this change. Please resubmit your changes.');
			} elseif ($this->insertTxn($acct,$this->user->getUserId(),$datestamp,$type,$establishment,$note,$credit,$debit,$parent_id,$banksays)) {
				if ($current_txn_id != 'null') {
					if ($this->makeTxnInactive($current_txn_id)) {
						$this->infoMsg->addMessage(2,'Transaction was successfully modified.');
						$return=TRUE; // Transaction was modified, and done so successfully
					}
				} else {
					if ($active_txn_id) {
						$this->infoMsg->addMessage(2,'Transaction was successfully made active.');
					} else {
						$this->infoMsg->addMessage(2,'Transaction was successfully added.');
					}
					$return=TRUE; // Transaction was added, and done so successfully
				}
			} else {
				$this->infoMsg->addMessage(-1,'An unexpected error occured while trying to add the transaction.');
			}
		} elseif ($current_txn_id == 'null') {
			$this->newTxnValues['type'] = $type;
			$this->newTxnValues['establishment'] = $establishment;
			$this->newTxnValues['note'] = $note;
			$this->newTxnValues['credit'] = $credit;
			$this->newTxnValues['debit'] = $debit;
		}
		$this->newTxnValues['acct'] = $acct; // Even if the entry was added correctly we want the account to stay the same
		$this->newTxnValues['date'] = $date; // Even if the entry was added correctly we want the date to stay the same
		return $return;
	}

	function validateNewTxn($date,$credit,$debit) {
		if (!strtotime($date)) {
			$this->infoMsg->addMessage(0,'Date was incorrectly formatted.');
			return FALSE;
		} elseif (($credit == NULL) and ($debit == NULL)) {
			$this->infoMsg->addMessage(0,'Credit or Debit require a value.');
			return FALSE;
		} elseif (($credit != NULL) and ($debit != NULL)) {
			$this->infoMsg->addMessage(0,'Credit and Debit cannot both have values.');
			return FALSE;
		} elseif (!(Normalize::validateCash($credit) and Normalize::validateCash($debit))) {
			$txnType = ($debit == NULL) ? 'Credit' : 'Debit';
			$this->infoMsg->addMessage(0,$txnType.' value is invalid.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function getTxnActiveStatus($current_txn_id) {
		$sql = "SELECT q_txn.active FROM q_txn WHERE q_txn.id = $current_txn_id;";
		return $this->DB->query($sql);
	}

	function insertTxn($acct,$user_id,$date,$type,$establishment,$note,$credit,$debit,$parent_id,$banksays) {
		if (($credit != NULL) and ($debit != NULL)) {
			$this->infoMsg->addMessage(-1,'Credit and debit have values, this transaction cannot be added.');
			return false;
		} elseif (($credit == NULL) and ($debit == NULL)) {
			$this->infoMsg->addMessage(-1,'Credit and debit do not have values, this transaction cannot be added.');
			return false;
		} else {
			$txn_type = ($debit == NULL) ? 'credit' : 'debit';
			$value = ($debit == NULL) ? $credit : $debit;
			$value = str_replace('$', '', $value);
			$banksays = ($banksays == 'on') ? 1 : 0;
			$entered = $this->user->getTime();
			$sql = "INSERT INTO q_txn (acct_id,user_id,entered,date,type,establishment,note,$txn_type,parent_txn_id,banksays,active)
				VALUES ($acct,$user_id,$entered,$date,'$type','$establishment','$note',$value,$parent_id,$banksays,1);";
			$return = $this->DB->query($sql);
			if ($parent_id == 'null') {
				$last_record_id = $this->DB->lastId();
				$sql = "UPDATE q_txn SET parent_txn_id = $last_record_id WHERE id = $last_record_id;";
				$this->DB->query($sql);
			}
			return $return;
		}
	}
}
?>