<?php
class Cipher {
	private $securekey;
	function __construct($textkey) {
		$this->securekey = md5($textkey);
	}
	function encrypt($input) {
		return urlencode(base64_encode(gzcompress(serialize($input) . $this->securekey)));
	}
	function decrypt($input) {
		// PHP5 appears to be auto decoding the URLs.
	  //return unserialize(substr(gzuncompress(base64_decode(urldecode($input))), 0, strlen(base64_decode(urldecode($input))) - strlen($this->securekey)));
		return unserialize(substr(gzuncompress(base64_decode($input)), 0, strlen(base64_decode(urldecode($input))) - strlen($this->securekey)));
	}
}
?>