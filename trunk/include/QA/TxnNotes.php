<?php
class QA_TxnNotes extends QA_Txns {
	private $txnNotes; // MySQL result
	private $userGroups; // MySQL result
	private $txnGroupNotes; // MySQL result

	function __construct($parentId,$sortId=NULL,$sortDir=NULL,$showAcct=NULL,$showMsgDiv=TRUE) {
		parent::__construct($parentId,$sortId=NULL,$sortDir=NULL,$showAcct=NULL,$showMsgDiv=TRUE);
	}

	function buildNotesWidget($parent_txn_id) {
		$this->getTxnNotes($parent_txn_id);
		$this->getUserGroups($parent_txn_id);
		$this->getTxnGroupNotes($parent_txn_id);
		$this->buildTxnNotes($this->container,$parent_txn_id);
		$this->printHTML();
	}

	public function addTxnNote($parent_txn_id, $note) {
		if ($this->insertTxnNote($parent_txn_id, $note)) {
			$this->infoMsg->addMessage(2,'Note was successfully added.');
		} else {
			$this->infoMsg->addMessage(-1,'An unexpected error occured while trying to add the note.');
		}
	}

	function getTxnNotes($parent_txn_id) {
		$sql = "SELECT q_txn_notes.*,user.handle
				FROM q_txn_notes,user
				WHERE q_txn_notes.txn_id={$parent_txn_id}
				  AND q_txn_notes.user_id = user.user_id
				ORDER BY q_txn_notes.posted DESC;";
		$this->txnNotes = $this->DB->query($sql);
	}

	function getUserGroups($parent_txn_id) {
		$sql = "SELECT q_user_groups.group_id,q_group.name
				FROM q_user_groups,q_group,q_share,q_txn
				WHERE q_user_groups.active = 1
				  AND q_user_groups.user_id = {$this->user->getUserId()}
				  AND q_user_groups.group_id = q_group.id
				  AND q_user_groups.group_id = q_share.group_id
				  AND q_share.acct_id = q_txn.acct_id
				  AND q_txn.parent_txn_id = $parent_txn_id
				  AND q_txn.active = 1
				ORDER BY q_group.name ASC;";
		$this->userGroups = $this->DB->query($sql);
	}

	function getTxnGroupNotes($txn_id,$group_num=NULL) {
		$group_num = ($group_num === NULL) ? $this->DB->num($this->userGroups) : $group_num;
		if ($group_num == 0) {
			$group_ids = " q_group_notes.group_id <> q_group_notes.group_id "; // No group ID ever equals 0
		} else {
			$current_group_id = $this->DB->fetch($this->userGroups);
			$group_ids = "q_group_notes.group_id = {$current_group_id['group_id']} ";
			while ($current_group_id = $this->DB->fetch($this->userGroups)){
				$group_ids .= ' OR q_group_notes.group_id='.$current_group_id['group_id'];
			}
		}
		$sql = "SELECT q_group_notes.*,user.handle
				FROM q_group_notes,user
				WHERE q_group_notes.txn_id={$txn_id}
				  AND ($group_ids)
				  AND q_group_notes.user_id = user.user_id
				ORDER BY q_group_notes.posted DESC;";
		$this->txnGroupNotes = $this->DB->query($sql);
	}

	function buildTxnNotes($parentElement,$parent_txn_id) {
		$notesDiv = new HTML_Div($parentElement,'txnn_'.$parent_txn_id,'txnn');
		$list = new UnorderedList($notesDiv,1);
		$this->buildNotesTab($notesDiv,$this->txnNotes,"Transaction",$tab=1,$parent_txn_id,$list);
		if ($this->DB->resetRowPointer($this->userGroups)) {
			while ($current_group = $this->DB->fetch($this->userGroups)) {
				$list->addItem();
				$this->buildNotesTab($notesDiv,$this->txnGroupNotes,$current_group['name'],++$tab,$parent_txn_id,$list);
			}
		}
	}

	function buildNotesTab($parentElement,$notesResult,$name,$tabNumber,$parentTxnId,$list) {
		new HTML_Anchor($list->items[$tabNumber-1],'#tab-'.$parentTxnId.'-'.$tabNumber,$name.' Notes');
		$notes = new HTML_Div($parentElement,'tab-'.$parentTxnId.'-'.$tabNumber);
		if ($this->DB->resetRowPointer($notesResult)) {
			$txnNoteTable = new Table($notes,$this->DB->num($notesResult)+1,3);
			$this->buildNotesTitles($txnNoteTable,$row=0);
			while ($current_note = $this->DB->fetch($notesResult)) {
				new HTML_Text($txnNoteTable->cells[++$row][$col=0],date($this->user->getDateFormat().' g:i:s a',$current_note['posted']));
				new HTML_Text($txnNoteTable->cells[$row][++$col],$current_note['handle']);
				new HTML_Text($txnNoteTable->cells[$row][++$col],$current_note['note']);
			}
		} else {
			new HTML_Text($notes,"There weren't any notes found for {$name}.");
		}
	}

	function buildNotesTitles($table,$row) {
		new HTML_Text($table->cells[$row][0],'Date');
		new HTML_Text($table->cells[$row][1],'User');
		new HTML_Text($table->cells[$row][2],'Note');
	}
}
?>