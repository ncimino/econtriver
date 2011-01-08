<?php
class QA_SelectTxns {

	function getTxnParentId($current_txn_id,$db) {
		$sql = "SELECT parent_txn_id FROM q_txn
					WHERE id = $current_txn_id;";
		$result = $db->fetch($db->query($sql));
		return $result['parent_txn_id'];
	}

	function getTxns($acctsToShow,$sortField,$sortDir,$db) {
		$sql = "SELECT q_txn.*,user.handle FROM q_txn,user,".QA_DB_Table::ACCT."
					WHERE (".$acctsToShow.")
					  AND q_txn.active = 1
					  AND q_txn.user_id = user.user_id
					  AND q_txn.acct_id = ".QA_DB_Table::ACCT.".id
					GROUP BY q_txn.id
					ORDER BY {$sortField} {$sortDir},q_txn.type ASC,q_txn.establishment ASC,q_txn.note ASC,q_txn.entered ASC;"; // Need to add next lvl search for consistent results
		return $db->query($sql);
	}

	function getTxnsSum($acctsToShow,$db) {
		return self::getCreditSum($acctsToShow,$db) - self::getDebitSum($acctsToShow,$db);
	}

	function getTxnsBankSaysSum($acctsToShow,$db) {
		return self::getCreditBankSaysSum($acctsToShow,$db) - self::getDebitBankSaysSum($acctsToShow,$db);
	}

	function getCreditSum($acctsToShow,$db) {
		$sql = "SELECT SUM(q_txn.credit) AS total FROM q_txn
					WHERE (".$acctsToShow.")
					  AND q_txn.active = 1;";
		$result = $db->fetch($db->query($sql));
		return $result['total'];
	}

	function getCreditBankSaysSum($acctsToShow,$db) {
		$sql = "SELECT SUM(q_txn.credit) AS total FROM q_txn
					WHERE (".$acctsToShow.")
					  AND q_txn.active = 1
					  AND q_txn.banksays = 1;";
		$result = $db->fetch($db->query($sql));
		return $result['total'];
	}

	function getDebitSum($acctsToShow,$db) {
		$sql = "SELECT SUM(q_txn.debit) AS total FROM q_txn
					WHERE (".$acctsToShow.")
					  AND q_txn.active = 1;";
		$result = $db->fetch($db->query($sql));
		return $result['total'];
	}

	function getDebitBankSaysSum($acctsToShow,$db) {
		$sql = "SELECT SUM(q_txn.debit) AS total FROM q_txn
					WHERE (".$acctsToShow.")
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