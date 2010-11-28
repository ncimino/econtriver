<?php
class AjaxQaSelectTxns {

	function getTxnParentId($current_txn_id,$db) {
		$sql = "SELECT parent_txn_id FROM q_txn
					WHERE id = $current_txn_id;";
		$result = $db->fetch($db->query($sql));
		return $result['parent_txn_id'];
	}

	function getTxns($sqlAcctsToShow,$sortField,$sortDir,$db) {
		$sql = "SELECT q_txn.*,user.handle FROM q_txn,user,q_acct
					WHERE (".$sqlAcctsToShow.")
					  AND q_txn.active = 1
					  AND q_txn.user_id = user.user_id
					  AND q_txn.acct_id = q_acct.id
					GROUP BY q_txn.id
					ORDER BY {$sortField} {$sortDir},q_txn.type ASC,q_txn.establishment ASC,q_txn.note ASC,q_txn.entered ASC;"; // Need to add next lvl search for consistent results
		return $db->query($sql);
	}

	function getTxnsSum($sqlAcctsToShow,$db) {
		return self::getCreditSum($sqlAcctsToShow,$db) - self::getDebitSum($sqlAcctsToShow,$db);
	}

	function getTxnsBankSaysSum($sqlAcctsToShow,$db) {
		return self::getCreditBankSaysSum($sqlAcctsToShow,$db) - self::getDebitBankSaysSum($sqlAcctsToShow,$db);
	}

	function getCreditSum($sqlAcctsToShow,$db) {
		$sql = "SELECT SUM(q_txn.credit) AS total FROM q_txn
					WHERE (".$sqlAcctsToShow.")
					  AND q_txn.active = 1;";
		$result = $db->fetch($db->query($sql));
		return $result['total'];
	}

	function getCreditBankSaysSum($sqlAcctsToShow,$db) {
		$sql = "SELECT SUM(q_txn.credit) AS total FROM q_txn
					WHERE (".$sqlAcctsToShow.")
					  AND q_txn.active = 1
					  AND q_txn.banksays = 1;";
		$result = $db->fetch($db->query($sql));
		return $result['total'];
	}

	function getDebitSum($sqlAcctsToShow,$db) {
		$sql = "SELECT SUM(q_txn.debit) AS total FROM q_txn
					WHERE (".$sqlAcctsToShow.")
					  AND q_txn.active = 1;";
		$result = $db->fetch($db->query($sql));
		return $result['total'];
	}

	function getDebitBankSaysSum($sqlAcctsToShow,$db) {
		$sql = "SELECT SUM(q_txn.debit) AS total FROM q_txn
					WHERE (".$sqlAcctsToShow.")
					  AND q_txn.active = 1
					  AND q_txn.banksays = 1;";
		$result = $db->fetch($db->query($sql));
		return $result['total'];
	}

	function getTxnActiveStatus($current_txn_id,$db) {
		$sql = "SELECT q_txn.active FROM q_txn WHERE q_txn.id = $current_txn_id;";
		return $db->query($sql);
	}
}
?>