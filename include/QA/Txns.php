<?php
class QA_Txns extends QA_Module {
	protected $activeAccounts; // MySQL result
	protected $ownedAccounts; // MySQL result
	protected $acctsToShow; // SQL syntax

	protected $activeTxns; // MySQL result
	protected $activeTxnsSum; // MySQL result
	protected $activeTxnsBankSaysSum; // MySQL result

	protected $newTxnValues = array();
	protected $sortDir = 'DESC';
	protected $sortField = 'q_txn.date';
	protected $selectedAcct = "0";

	function __construct($parentId,$sortId=NULL,$sortDir=NULL,$selectedAcct=NULL,$showMsgDiv=TRUE) {
		parent::__construct($showMsgDiv,5000);
		$this->parentId = $parentId;
		if (!$this->user->verifyUser()) {
			$this->infoMsg->addMessage(0,'User info is invalid, please login first.');
		} else {
			if (isset($sortDir)) $this->sortDir = $sortDir;
			if (isset($sortId)) $this->sortField = $this->convIdToField($sortId);
			if (isset($selectedAcct)) $this->setselectedAcct($selectedAcct);
			else $this->getselectedAcct();
			$this->newTxnValues['acct'] = "0";
			$this->newTxnValues['date'] = date($this->user->getDateFormat(),$this->user->getTime());
			$this->newTxnValues['type'] = '';
			$this->newTxnValues['establishment'] = '';
			$this->newTxnValues['note'] = '';
			$this->newTxnValues['credit'] = '';
			$this->newTxnValues['debit'] = '';
		}
	}

	function setselectedAcct($acctId) {
		if (QaSettings::setSetting('show_acct',$this->user->getUserId(),$acctId,$this->DB)) {
			$this->selectedAcct = $acctId;
		}
	}

	function getselectedAcct() {
		$this->selectedAcct = QaSettings::getSetting('show_acct',$this->user->getUserId(),$this->DB);
	}

	function convIdToField($input) {
		switch($input) {
			case '".QA_DB_Table::ACCT.".name': 				$return = 'txn_title_acctname';	break;
			case 'txn_title_acctname' :			$return = '".QA_DB_Table::ACCT.".name'; break;
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
			$link = new HTML_Anchor($parentElement,"#",'',$id,'txn_title');
			$curArrow = ($this->sortDir == 'DESC') ? 'ui-icon ui-icon-carat-1-s ui-float-right' : 'ui-icon ui-icon-carat-1-n ui-float-right';
			new HTML_Span($link,'',$id.'_'.$this->sortDir,$curArrow);
		} else {
			$link = new HTML_Anchor($parentElement,"#",'',$id,'txn_title');
		}
		new HTML_Text($link,$title);
	}

	function dropEntries($active_txn_id, $log, $current_txn_id=FALSE) {
		if (QA_ModifyTxns::makeTxnInactive($active_txn_id,$this->DB)) {
			if ($current_txn_id) { // If current_txn_id is defined, then the transaction was made active
				// This occurs when restoring a previous revision of the same transaction. The current must be made inactive.
				//$this->infoMsg->addMessage(2,'Transaction was successfully made active.');
			} else {
				$this->infoMsg->addMessage(2,'Transaction was successfully moved to the trash bin.');
				if ($log == 'true') {
					$parent_txn_id = QA_SelectTxns::getTxnParentId($active_txn_id,$this->DB);
					QA_ModifyNotes::insertTxnNote($parent_txn_id, "Deleted Transaction", $this->user, $this->DB, FALSE);
				}
			}
		} else {
			$this->infoMsg->addMessage(-1,'An unexpected error occurred while trying to move the transaction to the trash.');
		}
	}

	function restoreEntries($current_txn_id) {
		if (QA_ModifyTxns::makeTxnActive($current_txn_id,$this->DB)) {
			$this->infoMsg->addMessage(2,'Transaction was successfully restored.');
			$parent_txn_id = QA_SelectTxns::getTxnParentId($current_txn_id,$this->DB);
			QA_ModifyNotes::insertTxnNote($parent_txn_id, "Restored Transaction", $this->user, $user->DB, FALSE);
		} else {
			$this->infoMsg->addMessage(-1,'An unexpected error occurred while trying to restore the transaction.');
		}
	}

	function createWidget() {
		$this->activeAccounts = QA_Account_Select::byMember($this->user->getUserId(),$this->DB);
		$this->ownedAccounts = QA_Account_Select::owned($this->user->getUserId(),$this->DB);
		$this->acctsToShow = QA_Account_Select::acctsToShow($this->selectedAcct,$this->activeAccounts,$this->user->getUserId(),$this->DB);
		if ($this->DB->num($this->activeAccounts)) {
			$this->buildActions();
			$this->buildTxnsTable();
		} else {
			new HTML_Text($this->container,'There are no active accounts. You must have an active account before you can add transactions.');
		}
		$this->printHTML();
	}

	function buildActions() {
		$divActions = new HTML_Div($this->container,'txn_actions_id','txn_actions');
		$jsDateFormat = ($this->user->getDateFormat() == "Y-m-d") ? "yy-mm-dd" : "mm/dd/yy"; // else "m/d/Y"
		new HTML_InputHidden($divActions,'date_format',$jsDateFormat,'date_format');
		$this->buildActionsAcctsDropDown($divActions,'show_acct','show_acct',NULL,$this->selectedAcct);
		$this->buildActionsTray($divActions);
		$divActionContent = new HTML_Div($divActions,'txn_actions_content');
		$divActionContent->setAttribute('style','display:none;');
	}

	function buildActionsTray($parentElement) {
		$divActionButtons = new HTML_Div($parentElement,'txn_actions_buttons');
		$divActionButtons->setStyle('float: right;');

		$h3Actions = new HTML_Heading($divActionButtons,5,'Actions: ');
		$h3Actions->setStyle('float: left;margin: 2px;padding 0px;');

		$trashTxn = new HTML_Anchor($divActionButtons,'#','','txn_show_trash_anchor','txn_show_trash');
		$trashTxn->setTitle('Trash Bin');
		new HTML_Span($trashTxn,'','txn_show_trash','ui-icon ui-icon-trash ui-float-right');

		$printTxn = new HTML_Anchor($divActionButtons,'#','','txn_print_anchor');
		$printTxn->setTitle('Open Print View');
		new HTML_Span($printTxn,'','txn_print','ui-icon-inactive ui-icon-print ui-float-right');

		$overviewTxn = new HTML_Anchor($divActionButtons,'#','','txn_show_overview_anchor','txn_show_overview');
		$overviewTxn->setTitle('Show Overview');
		new HTML_Span($overviewTxn,'','txn_show_overview','ui-icon-inactive ui-icon-clipboard ui-float-right');
	}

	function buildActionsAcctsDropDown($parentElement,$name=NULL,$id=NULL,$class=NULL,$selectedAcct=NULL) {
		$h3View = new HTML_Heading($parentElement,5,'View Account: ');
		$h3View->setStyle('float: left;margin: 4px;padding 0px;');
		$allAccountsIndex = 0;
		$selectAcctMenu = new DropDownMenu($parentElement,$name,$id,$class);
		$selectAcctMenu->addOption('All Accounts',$allAccountsIndex,($allAccountsIndex === $selectedAcct),NULL,'dropdown_group');
		$this->buildActionAcctsForDropDown($selectAcctMenu,'My Accounts',$this->user->getUserId(),'u'.$this->user->getUserId(),$selectedAcct);
		$activeContacts = QA_SelectGroupMembers::getAssociatedActiveContactsForAllGroups($this->user->getUserId(),$this->DB);
		while($result = $this->DB->fetch($activeContacts)) {
			if ($this->user->getUserId() != $result['user_id']) {
				$this->buildActionAcctsForDropDown($selectAcctMenu,$result['handle'].'\'s Accounts',$result['user_id'],'u'.$result['user_id'],$selectedAcct);
			}
		}
	}

	function buildActionAcctsForDropDown($selectAcctMenu,$groupName,$userId,$grpId,$selectedAcct) {
		$allAccountsIndex = 0;
		$ownedAccountsForUser = QA_Account_Select::sharedForOwner($userId,$this->user->getUserId(),$this->DB);
		if (($userId == $this->user->getUserId()) or ($this->DB->num($ownedAccountsForUser))) {
			$selectAcctMenu->addOption($groupName,$grpId,($selectedAcct == $grpId),NULL,'dropdown_group');
			$this->DB->resetRowPointer($this->activeAccounts);
			while($result = $this->DB->fetch($this->activeAccounts)) {
				if ($result['owner_id'] == $userId) {
					$selectAcctMenu->addOption('&nbsp;&nbsp;'.$result['name'],$result['id'],($selectedAcct==$result['id']));
				}
			}
		}
	}

	function buildAcctsDropDown($parentElement,$name=NULL,$id=NULL,$class=NULL,$selectedAcct=NULL) {
		$allAccountsIndex = 0;
		$selectAcctMenu = new DropDownMenu($parentElement,$name,$id,$class);
		$accountSelected = $this->buildAcctsForDropDown($selectAcctMenu,'My Accounts',$this->user->getUserId(),'u'.$this->user->getUserId(),$selectedAcct);
		$activeContacts = QA_SelectGroupMembers::getAssociatedActiveContactsForAllGroups($this->user->getUserId(),$this->DB);
		while($result = $this->DB->fetch($activeContacts)) {
			if ($this->user->getUserId() != $result['user_id']) {
				$this->buildAcctsForDropDown($selectAcctMenu,$result['handle'].'\'s Accounts',$result['user_id'],'u'.$result['user_id'],$selectedAcct);
			}
		}
		$selectAcctMenu->setSelected(1);
		return $selectAcctMenu->menu;
	}

	function buildAcctsForDropDown($selectAcctMenu,$groupName,$userId,$grpId,$selectedAcct) {
		$allAccountsIndex = 0;
		$selectAcctMenu->addOption($groupName,$grpId,FALSE);
		$selectAcctMenu->disableOption();
		$this->DB->resetRowPointer($this->activeAccounts);
		while($result = $this->DB->fetch($this->activeAccounts)) {
			if ($result['owner_id'] == $userId) {
				$selectAcctMenu->addOption($result['name'],$result['id'],($selectedAcct==$result['id']));
			}
		}
	}

	function buildTxnsTable() {
		$divNew = new HTML_Div($this->container,'txn_id','txn');
		$this->activeTxns = QA_SelectTxns::getTxns($this->acctsToShow,$this->sortField,$this->sortDir,$this->DB);
		$this->activeTxnsSum = QA_SelectTxns::getTxnsSum($this->acctsToShow,$this->DB);
		$this->activeTxnsBankSaysSum = QA_SelectTxns::getTxnsBankSaysSum($this->acctsToShow,$this->DB);
		$rows = $this->DB->num($this->activeTxns) * 3 + 2;
		$tableTxn = new Table($divNew,$rows,12,'txn_table','txn');
		$this->buildTxnTitles($tableTxn,0);
		$this->buildNewTxns($tableTxn,1);
		$this->buildTxns($tableTxn,2);
	}

	function buildTxnTitles($tableTxn,$row) {
		$col = 0;

		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Account','".QA_DB_Table::ACCT.".name',$this->convIdToField('".QA_DB_Table::ACCT.".name'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'User','user.handle',$this->convIdToField('user.handle'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Entered','q_txn.entered',$this->convIdToField('q_txn.entered'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Date','q_txn.date',$this->convIdToField('q_txn.date'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Type','q_txn.type',$this->convIdToField('q_txn.type'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Establishment','q_txn.establishment',$this->convIdToField('q_txn.establishment'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Note','q_txn.note',$this->convIdToField('q_txn.note'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Credit','q_txn.credit',$this->convIdToField('q_txn.credit'));
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Debit','q_txn.debit',$this->convIdToField('q_txn.debit'));
		new HTML_Text($tableTxn->cells[$row][$col++],'Balance');
		$this->addSortableTitle($tableTxn->cells[$row][$col++],'Bank Says','q_txn.banksays',$this->convIdToField('q_txn.banksays')); //:BUG:Fixes issue 42
		new HTML_Text($tableTxn->cells[$row][$col++],'Actions');
	}

	function buildNewTxns($tableTxn,$row) {
		$col = 0;
		$selectedAcct = ($this->newTxnValues['acct']) ? $this->newTxnValues['acct'] : $this->selectedAcct; // If a txn was entered use that
		$account_dd = $this->buildAcctsDropDown($tableTxn->cells[$row][$col++],'new_txn_acct','new_txn_acct','txn_input',$selectedAcct);
		$this->tabIndex->add($account_dd);
		new HTML_Text($tableTxn->cells[$row][$col++],'-'); // User
		new HTML_Text($tableTxn->cells[$row][$col++],'-'); // Entered
		$txn_current_tag = new HTML_InputText($tableTxn->cells[$row][$col++],'new_txn_date',$this->newTxnValues['date'],'new_txn_date','dateselection txn_input');
		$this->tabIndex->add($txn_current_tag);
		$txn_current_tag = new HTML_InputText($tableTxn->cells[$row][$col++],'new_txn_type',$this->newTxnValues['type'],'new_txn_type','txn_input autocomplete_type');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_establishment\')');
		$this->tabIndex->add($txn_current_tag);
		$txn_current_tag = new HTML_InputText($tableTxn->cells[$row][$col++],'new_txn_establishment',$this->newTxnValues['establishment'],'new_txn_establishment','txn_input autocomplete_establishment');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_note\')');
		$this->tabIndex->add($txn_current_tag);
		$txn_current_tag = new HTML_InputText($tableTxn->cells[$row][$col++],'new_txn_note',$this->newTxnValues['note'],'new_txn_note','txn_input autocomplete_note');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_credit\')');
		$this->tabIndex->add($txn_current_tag);
		$txn_current_tag = new HTML_InputText($tableTxn->cells[$row][$col++],'new_txn_credit',$this->newTxnValues['credit'],'new_txn_credit','txn_input credit');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_debit\')');
		$this->tabIndex->add($txn_current_tag);
		$txn_current_tag = new HTML_InputText($tableTxn->cells[$row][$col++],'new_txn_debit',$this->newTxnValues['debit'],'new_txn_debit','txn_input debit');
		$txn_current_tag->setAttribute('onkeyup','enterFocus(event,\'new_txn_submit\')');
		$this->tabIndex->add($txn_current_tag);
		new HTML_Text($tableTxn->cells[$row][$col++],'-'); // Balance
		new HTML_Text($tableTxn->cells[$row][$col++],'-'); // Bank Says

		/*ACTIONS*/

		$submitNew = new HTML_Anchor($tableTxn->cells[$row][$col],'#','','txn_add');
		//$submitNew->setAttribute('onkeyup','enterCall(event,function() {QaTxnAdd(\'new_txn_\');})');
		$submitNew->setTitle("Add");
		$submitNewSpan = new HTML_Span($submitNew,'','new_txn_submit','ui-icon ui-icon-plusthick ui-float-left');
		$this->tabIndex->add($submitNewSpan);

		$splitNew = new HTML_Anchor($tableTxn->cells[$row][$col],'#','','txn_split');
		$splitNew->setTitle("Transfer");
		$splitNewSpan = new HTML_Span($splitNew,'','new_txn_split','ui-icon ui-icon-transferthick-e-w ui-float-left');
		$this->tabIndex->add($splitNewSpan);
	}

	function buildTxns($tableTxn,$row) {
		if ($this->activeTxns) {
			$currentBalance = $this->activeTxnsSum;
			$currentBankSays = $this->activeTxnsBankSaysSum;
			while($txn = $this->DB->fetch($this->activeTxns)) {
				$col = 0;
				$oddOrEven = ($row % 2 == 0) ? "odd" : "even";

				$account_dd = $this->buildAcctsDropDown($tableTxn->cells[$row][$col++],'txn_acct_'.$txn['id'],'txn_acct_'.$txn['id'],'txn_acct_select_'.$oddOrEven,$txn['acct_id']);
				$this->tabIndex->add($account_dd);

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable');
				new HTML_Text($tableTxn->cells[$row][$col++],$txn['handle']);

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' non_editable number');
				new HTML_Text($tableTxn->cells[$row][$col++],date($this->user->getDateFormat(),$txn['entered']));

				$txn_current_tag = new HTML_InputText($tableTxn->cells[$row][$col++],'txn_date_'.$txn['id'],date($this->user->getDateFormat(),$txn['date']),'txn_date_'.$txn['id'],'dateselection txn_input number');
				$this->tabIndex->add($txn_current_tag);
				$txn_current_tag = new HTML_InputText($tableTxn->cells[$row][$col++],'txn_type_'.$txn['id'],$txn['type'],'txn_type_'.$txn['id'],'txn_input');
				$this->tabIndex->add($txn_current_tag);
				$txn_current_tag = new HTML_InputText($tableTxn->cells[$row][$col++],'txn_establishment_'.$txn['id'],$txn['establishment'],'txn_establishment_'.$txn['id'],'txn_input');
				$this->tabIndex->add($txn_current_tag);
				$txn_current_tag = new HTML_InputText($tableTxn->cells[$row][$col++],'txn_note_'.$txn['id'],$txn['note'],'txn_note_'.$txn['id'],'txn_input');
				$this->tabIndex->add($txn_current_tag);
				$txn_current_tag = new HTML_InputText($tableTxn->cells[$row][$col++],'txn_credit_'.$txn['id'],$txn['credit'],'txn_credit_'.$txn['id'],'txn_input number credit');
				$this->tabIndex->add($txn_current_tag);
				$txn_current_tag = new HTML_InputText($tableTxn->cells[$row][$col++],'txn_debit_'.$txn['id'],$txn['debit'],'txn_debit_'.$txn['id'],'txn_input number debit');
				$this->tabIndex->add($txn_current_tag);

				$posNeg = (round($currentBalance,2)>=0) ? ' positive' : ' negative';
				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' number balance'.$posNeg);
				new HTML_Text($tableTxn->cells[$row][$col++],number_format(round($currentBalance,2),2));

				$posNeg = (round($currentBankSays,2)>=0) ? ' positive' : ' negative';
				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' number bank_says'.$posNeg);
				$checked = ($txn['banksays']) ? TRUE : FALSE;
				$banksays_cb = new HTML_InputCheckbox($tableTxn->cells[$row][$col],'txn_banksays_'.$txn['id'],'txn_banksays_'.$txn['id'],'txn_banksays_check',$checked);
				$this->tabIndex->add($banksays_cb);
				$parent_id = ($txn['parent_txn_id']) ? $txn['parent_txn_id'] : $txn['id'];
				new HTML_Text($tableTxn->cells[$row][$col++],number_format(round($currentBankSays,2),2));

				$currentBalance = $currentBalance + $txn['debit'] - $txn['credit'];
				if ($checked) $currentBankSays = $currentBankSays + $txn['debit'] - $txn['credit'];

				/* ACTIONS */

				$saveTxn = new HTML_Anchor($tableTxn->cells[$row][$col],'#','','txn_save_anchor_'.$txn['id'],'');
				$saveTxn->setTitle('Save');
				$iconSpan = new HTML_Span($saveTxn,'','txn_save_'.$txn['id'],'ui-icon-inactive ui-icon-disk ui-float-left');
				$this->tabIndex->add($iconSpan);

				$tableTxn->cells[$row][$col]->setClass($tableTxn->cells[$row][$col]->getClass().' per_txn_actions');
				if ($txn['id'] != $txn['parent_txn_id']) {
					$showHistory = new HTML_Anchor($tableTxn->cells[$row][$col],'#','','txn_show_history_anchor_'.$txn['id'],'txn_show_history');
					$iconSpan = new HTML_Span($showHistory,'','txn_show_history_'.$txn['id'],'ui-icon ui-icon-clock ui-float-left');
				} else {
					$showHistory = new HTML_Anchor($tableTxn->cells[$row][$col],'#','','');
					$iconSpan = new HTML_Span($showHistory,'','txn_show_history_'.$txn['id'],'ui-icon-inactive ui-icon-clock ui-float-left');
				}
				$showHistory->setTitle('History');
				$this->tabIndex->add($iconSpan);

				$showNotes = new HTML_Anchor($tableTxn->cells[$row][$col],'#','','txn_show_notes_'.$txn['id'],'txn_show_notes');
				$showNotes->setTitle('Notes');
				$iconSpan = new HTML_Span($showNotes,'','txn_show_notes_'.$txn['id'],'ui-icon ui-icon-note ui-float-left');
				$this->tabIndex->add($iconSpan);

				$deleteTxn = new HTML_Anchor($tableTxn->cells[$row][$col],'#','','txn_delete_anchor_'.$txn['id'],'txn_delete');
				$deleteTxn->setTitle('Delete');
				$iconSpan = new HTML_Span($deleteTxn,'','txn_delete_'.$txn['id'],'ui-icon ui-icon-trash ui-float-left');
				$this->tabIndex->add($iconSpan);

				new HTML_InputHidden($tableTxn->cells[$row][$col],'txn_parent_id_'.$txn['id'],$parent_id,'txn_parent_id_'.$txn['id']);

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
			QA_SelectTxns::getTxnActiveStatus($current_txn_id,$this->DB);
			$txnActiveStatus = $this->DB->fetch();
			if (($this->DB->num() >= 1) and ($txnActiveStatus['active'] == 0)) { // If current txn id exists, but is inactive
				$this->infoMsg->addMessage(0,'This entry was modified before you submitted this change. Please resubmit your changes.');
			} elseif ($this->insertTxn($acct,$this->user->getUserId(),$datestamp,$type,$establishment,$note,$credit,$debit,$parent_id,$banksays)) {
				if ($current_txn_id != 'null') {
					if (QA_ModifyTxns::makeTxnInactive($current_txn_id,$this->DB)) {
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

	function insertTxn($acct,$user_id,$date,$type,$establishment,$note,$credit,$debit,$parent_id,$banksays) {
		if (($credit != NULL) and ($debit != NULL)) {
			$this->infoMsg->addMessage(-1,'Credit and debit have values, this transaction cannot be added.');
			return false;
		} elseif (($credit == NULL) and ($debit == NULL)) {
			$this->infoMsg->addMessage(-1,'Credit and debit do not have values, this transaction cannot be added.');
			return false;
		} else {
			return QA_ModifyTxns::insertTxn($acct,$user_id,$date,$type,$establishment,$note,$credit,$debit,$parent_id,$banksays,$this->user,$this->DB);
		}
	}
}
?>