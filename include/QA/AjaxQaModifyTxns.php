<?php
class QA_ModifyTxns {
	static function makeTxnInactive($current_txn_id,$db) {
		$sql = "UPDATE q_txn SET active = 0 WHERE q_txn.id = $current_txn_id;";
		return $db->query($sql);
	}

	static function makeTxnActive($current_txn_id,$db) {
		$sql = "UPDATE q_txn SET active = 1 WHERE q_txn.id = $current_txn_id;";
		return $db->query($sql);
	}

	static function insertTxn($acct,$user_id,$date,$type,$establishment,$note,$credit,$debit,$parent_id,$banksays,$user,$db) {
		$txn_type = ($debit == NULL) ? 'credit' : 'debit';
		$value = ($debit == NULL) ? $credit : $debit;
		$value = str_replace('$', '', $value);
		$banksays = ($banksays == 'on') ? 1 : 0;
		$entered = $user->getTime();
		self::insertNewTxn($acct,$user_id,$date,$type,$establishment,$note,$credit,$debit,$parent_id,$banksays,$user,$db);
		if ($parent_id == 'null') {
			self::updateParentTxnId($db->lastId(),$db->lastId());
		}
		return $return;
	}
	
	static function insertNewTxn($acct,$user_id,$date,$type,$establishment,$note,$credit,$debit,$parent_id,$banksays,$user,$db) {
		$sql = "INSERT INTO q_txn (acct_id,user_id,entered,date,type,establishment,note,$txn_type,parent_txn_id,banksays,active)
				VALUES ($acct,$user_id,$entered,$date,'$type','$establishment','$note',$value,$parent_id,$banksays,1);";
		$return = $db->query($sql);
	}

	static function updateParentTxnId($update_txn_id,$set_parent_id_to) {
		$sql = "UPDATE q_txn SET parent_txn_id = $set_parent_id_to WHERE id = $update_txn_id;";
		return $db->query($sql);
	}
}
?>