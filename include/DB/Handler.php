<?php
class DB_Handler {
	private $sql;
	private $db;
	
	public function __construct($sql) {
		$this->db = new DBCon();
		$this->setSQL($sql);
		$this->modifyDb();
	}
	
	private function setSQL($sql) {	$this->sql = $sql; }
	
	public function modifyDb() {
		try {
			$this->db->query($this->sql);
			echo "COMPLETED:<BR>\n".$this->sql."<BR>\n";
		} catch (Exception $e) { new ExceptionHandler($e); }
	} 
}
?>