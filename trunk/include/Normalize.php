<?php
class Normalize {

	static function encodeBs($value) { return addcslashes($value,'\\'); }
	static function encodeFs($value) { return addcslashes($value,'\/'); }
	static function mysql($value) { return mysql_real_escape_string($value); }
	static function tags($value) { return strip_tags($value); }
	static function accountNames($value,$infoMsg) {
		if (preg_match('/^[a-zA-Z0-9_ -]+$/',$value)) {
			return true;
		} else {
			$infoMsg->addMessage(0,'Account names can only contain numbers, letters, spaces, hyphens, and underscores.');
			return false;
		}
	}
	static function groupNames($value,$infoMsg) {
		if (preg_match('/^[a-zA-Z0-9_ -]+$/',$value)) {
			return true;
		} else {
			$infoMsg->addMessage(0,'Group names can contain only numbers, letters, spaces, hyphens, and underscores.');
			return false;
		}
	}
	static function validateCash($value) {
		if (preg_match('/^((\$\d*)|(\$\d*\.\d{2})|(\d*)|(\d*\.\d{2}))$/',$value)) {
			return true;
		} else {
			return false;
		}
	}
	static function sanitize($value,$infoMsg,$siteInfo) {
		$value = htmlentities($value);
		if (empty($value)) {
			return false;
		} elseif (!($siteInfo->verifyReferer())) {
			$infoMsg->addMessage(0,'You cannot enter data using another website.');
			return false;
		} else {
			return $value;
		}
	}
	static function innerFragment($HTML_Document) {
		$htmlString = $HTML_Document->saveXML();
		$htmlString = explode('<fragment>',$htmlString);
		$htmlString = explode('</fragment>',$htmlString[1]);
		return $htmlString[0];
	}
	static function stripWhiteSpace($string) {
		return preg_replace('/\s/','',$string);
	}
}