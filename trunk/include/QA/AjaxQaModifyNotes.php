<?php
class QA_ModifyNotes {
	static function insertTxnNote($parent_txn_id, $note, $user, $db, $editable=TRUE) {
		$edited = ($editable) ? "null" : "1";
		$clean_note = Normalize::tags($note);
		$sql = "INSERT INTO q_txn_notes (user_id,txn_id,posted,note,edited)
				VALUES ({$user->getUserId()}, $parent_txn_id, {$user->getTime()}, '$clean_note', $edited);";
		return $db->query($sql);
	}
}
?>