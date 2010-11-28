<?php
class DBCon {
	private $database;
	private $link;
	private $result;
	private $ini_file = "vars/db.ini";
	private $ini_arr = array();
	public $sql;

	function __construct($select_bool=true) {
		$this->parseDbIniFile();
		$this->connect();
		if ($select_bool)
		{
			$this->selectDB();
			return $this->link;
		} else {
			return true;
		}
	}
	
	private function parseDbIniFile() {
		if (! $this->ini_arr = parse_ini_file($this->ini_file)) {
			throw new exception("Failed to parse '{$this->ini_file}'");
		}
	}

	public function connect() {
		if (! $this->link = mysql_connect($this->ini_arr['dbinfo.host'],$this->ini_arr['dbinfo.user'],$this->ini_arr['dbinfo.pw'])) {
			throw new exception(mysql_error());
		}
	}

	public function selectDB() {
		if (! mysql_select_db($this->ini_arr['dbinfo.db'],$this->link)) {
			throw new exception(mysql_error());
		}
	}

	public function query($sql) {
		$this->sql = $sql;
		if (! $this->result = mysql_query($sql,$this->link)) {
			throw new exception(mysql_error()." - Trying to execute " . $sql);
			return false;
		} else {
			return $this->result;
		}
	}

	public function num($result="") {
		if (empty($result)){ $result = $this->result; } 
		return (empty($result)) ? FALSE : mysql_num_rows($result);
	}

	public function lastId() { return mysql_insert_id($this->link);	}

	public function fetch($result="") {
		if (empty($result)){ $result = $this->result; }
		return mysql_fetch_array($result);
	}

	public function resetRowPointer($result="") {
		if (empty($result)){ 
			$result = $this->result; 
		}
		return ($this->num($result) and mysql_data_seek($result,0));
	}

	public function setDB($database="") { $this->database = $database; }
	public function setINIFile($ini_file="") { $this->ini_file = $ini_file; }

	public function getDB() { 
		if ($this->database=="") {
			return $this->ini_arr['dbinfo.db'];
		} else {
			return $this->database; 
		}
	}
	
	public function getINIFile() { return $this->ini_file; }
	public function __destruct(){ mysql_close($this->link); }
}
?>